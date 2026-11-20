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
            "\u{1F9FE} PESANAN BARU",
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
            "\u{2705} PEMBAYARAN DIKONFIRMASI",
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

        // Escape sequence Unicode dipakai agar emoji tidak rusak saat file
        // dipindah/deploy dengan encoding yang berbeda (mis. Vercel/Windows).
        $divider = str_repeat("\u{2500}", 20);

        $lines = [
            $title,
            (string) config('pharmacy.name'),
            $divider,
            "\u{1F4CC} Invoice     : ".$order->invoice_number,
            "\u{1F4CA} Status      : ".$status,
            "\u{1F464} Pemesan     : ".($order->recipient_name ?: $order->customer?->name ?: '-')
                .' ('.($order->recipient_phone ?: $order->customer?->phone ?: '-').')',
            "\u{1F4B3} Pembayaran  : ".$this->paymentLabel((string) $order->payment_method),
            "\u{1F69A} Pengiriman  : ".$this->deliveryLabel((string) $order->delivery_method),
            $divider,
            "\u{1F6D2} RINCIAN PESANAN",
        ];

        foreach ($order->details as $detail) {
            $name = $detail->productBatch?->product?->name ?? 'Obat';
            $unit = $detail->productBatch?->product?->unit ?? 'pcs';

            $lines[] = "\u{2022} ".$name;
            $lines[] = sprintf(
                "   %d %s \u{00D7} Rp %s = Rp %s",
                (int) $detail->quantity,
                $unit,
                $this->rupiah($detail->price),
                $this->rupiah($detail->subtotal)
            );
        }

        $lines[] = $divider;
        $lines[] = '   Subtotal    Rp '.$this->rupiah($subtotal);
        $lines[] = '   Ongkir      '.($deliveryFee > 0 ? 'Rp '.$this->rupiah($deliveryFee) : 'Gratis');
        $lines[] = "\u{1F4B0} TOTAL       Rp ".$this->rupiah($order->total_amount);
        $lines[] = $divider;

        if ($order->delivery_address) {
            $lines[] = "\u{1F4CD} Alamat      : ".preg_replace('/\s+/', ' ', trim($order->delivery_address));
        }

        if ($order->order_notes) {
            $lines[] = "\u{1F4DD} Catatan     : ".$order->order_notes;
        }

        $lines[] = "\u{1F517} Detail      : ".route('portal.order.show', $order->invoice_number, true);
        $lines[] = '';
        $lines[] = "Terima kasih sudah berbelanja \u{1F64F}";

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
