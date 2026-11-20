<?php

namespace App\Services;

use App\Models\SalesTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Kirim notifikasi pembayaran ke Telegram via Bot API.
 *
 * Service ini sengaja "fail-safe": bila TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID
 * belum diset (mis. di Vercel), atau request gagal, notifikasi dilewati dan
 * aplikasi tetap berjalan normal. Tidak ada exception yang diteruskan.
 */
class PaymentNotifier
{
    /**
     * Notifikasi saat checkout/new order berhasil dibuat (status pending).
     */
    public function orderPlaced(SalesTransaction $order): bool
    {
        return $this->send($this->buildMessage(
            $order,
            '[ PESANAN BARU ]',
            'Menunggu konfirmasi (pending)'
        ));
    }

    /**
     * Notifikasi saat pembayaran dikonfirmasi petugas (status paid).
     */
    public function orderPaid(SalesTransaction $order): bool
    {
        return $this->send($this->buildMessage(
            $order,
            '[ PEMBAYARAN DIKONFIRMASI ]',
            'Lunas (paid)'
        ));
    }

    /**
     * Susun pesan ringkasan pesanan untuk Telegram / WhatsApp.
     */
    public function buildMessage(SalesTransaction $order, string $title, string $status): string
    {
        $order->loadMissing('details.productBatch.product');

        $subtotal = (float) $order->total_amount - (float) $order->delivery_fee;
        $deliveryFee = (float) $order->delivery_fee;

        // Teks ringkasan sengaja memakai penanda ASCII agar tidak bergantung
        // pada font emoji. Emoji sering tampil sebagai kotak "?" di host/browser
        // yang tidak memiliki glyph emoji, meskipun bytes UTF-8-nya sudah benar.
        $divider = str_repeat('-', 20);

        $lines = [
            $title,
            (string) config('pharmacy.name'),
            $divider,
            'Invoice     : '.$order->invoice_number,
            'Status      : '.$status,
            'Pemesan     : '.($order->recipient_name ?: $order->customer?->name ?: '-')
                .' ('.($order->recipient_phone ?: $order->customer?->phone ?: '-').')',
            'Pembayaran  : '.$this->paymentLabel((string) $order->payment_method),
            'Pengiriman  : '.$this->deliveryLabel((string) $order->delivery_method),
            $divider,
            'RINCIAN PESANAN',
        ];

        foreach ($order->details as $detail) {
            $name = $detail->productBatch?->product?->name ?? 'Obat';
            $unit = $detail->productBatch?->product?->unit ?? 'pcs';

            $lines[] = '- '.$name;
            $lines[] = sprintf(
                '   %d %s x Rp %s = Rp %s',
                (int) $detail->quantity,
                $unit,
                $this->rupiah($detail->price),
                $this->rupiah($detail->subtotal)
            );
        }

        $lines[] = $divider;
        $lines[] = '   Subtotal    Rp '.$this->rupiah($subtotal);
        $lines[] = '   Ongkir      '.($deliveryFee > 0 ? 'Rp '.$this->rupiah($deliveryFee) : 'Gratis');
        $lines[] = 'TOTAL       Rp '.$this->rupiah($order->total_amount);
        $lines[] = $divider;

        if ($order->delivery_address) {
            $lines[] = 'Alamat      : '.preg_replace('/\s+/', ' ', trim($order->delivery_address));
        }

        if ($order->order_notes) {
            $lines[] = 'Catatan     : '.$order->order_notes;
        }

        $lines[] = 'Detail      : '.route('portal.order.show', $order->invoice_number, true);
        $lines[] = '';
        $lines[] = 'Terima kasih sudah berbelanja.';

        return implode("\n", $lines);
    }

    /**
     * Kirim pesan ke Telegram Bot API. Selalu mengembalikan bool.
     */
    public function send(string $message): bool
    {
        $token = trim((string) config('services.telegram.bot_token', ''));
        $chatId = trim((string) config('services.telegram.chat_id', ''));

        // Belum dikonfigurasi -> diam-diam dilewati (aman di Vercel).
        if ($token === '' || $chatId === '') {
            return false;
        }

        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'                  => $chatId,
                    'text'                     => $message,
                    'disable_web_page_preview' => true,
                ]);

            if (! $response->successful()) {
                Log::warning('[telegram] notifikasi gagal dikirim', [
                    'status' => $response->status(),
                    'body'   => mb_substr($response->body(), 0, 300),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $exception) {
            // Jangan pernah menggagalkan checkout/konfirmasi karena notifikasi.
            Log::warning('[telegram] notifikasi dilewati: '.$exception->getMessage());

            return false;
        }
    }

    private function paymentLabel(string $method): string
    {
        return [
            'cod'      => 'COD (Bayar di Tempat)',
            'transfer' => 'Transfer Bank',
            'qris'     => 'QRIS',
            'cash'     => 'Tunai',
            'debit'    => 'Debit',
        ][$method] ?? strtoupper($method);
    }

    private function deliveryLabel(string $method): string
    {
        return [
            'pickup'   => 'Ambil sendiri di toko',
            'delivery' => 'Kirim ke alamat',
        ][$method] ?? strtoupper($method ?: '-');
    }

    private function rupiah(mixed $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }
}
