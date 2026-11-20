<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\SalesTransaction;
use App\Models\User;
use App\Services\PaymentNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private ProductBatch $batch;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Pesanan tamu selalu dicatat atas nama petugas sistem (admin pertama).
        $this->admin = User::create([
            'name'     => 'Admin Apotek',
            'email'    => 'admin-test@apotek.com',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        $category = Category::create(['name' => 'Analgesik']);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Paracetamol 500mg',
            'unit'        => 'Strip',
            'min_stock'   => 5,
        ]);

        $this->batch = ProductBatch::create([
            'product_id'   => $this->product->id,
            'batch_number' => 'BATCH-TEST-01',
            'buy_price'    => 2500,
            'sell_price'   => 3500,
            'stock'        => 20,
            'expired_date' => now()->addYear()->toDateString(),
        ]);
    }

    public function test_catalog_shows_product_price_and_stock(): void
    {
        $response = $this->get(route('portal.index'));

        $response->assertOk();
        $response->assertSee('Paracetamol 500mg');
        $response->assertSee('Rp 3.500');
    }

    public function test_guest_can_submit_delivery_order_and_it_becomes_pending(): void
    {
        $response = $this->post(route('portal.checkout.store'), [
            'customer_name'    => 'Retno Mey Puspita',
            'customer_phone'   => '081234567890',
            'delivery_method'  => 'delivery',
            'payment_method'   => 'transfer',
            'order_notes'      => 'Tolong dikirim sore hari.',
            'items'            => json_encode([
                ['product_id' => $this->product->id, 'quantity' => 3],
            ]),
            'address_street'   => 'Jl. Raya Punggur No. 45',
            'address_village'  => 'Tanggul Angin',
            'address_district' => 'Punggur',
            'address_city'     => 'Lampung Tengah',
            'address_postal'   => '34161',
        ]);

        $order = SalesTransaction::where('order_channel', 'online')->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('portal.order.show', $order->invoice_number));

        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('transfer', $order->payment_method);
        $this->assertSame('delivery', $order->delivery_method);
        $this->assertSame('081234567890', $order->recipient_phone);
        $this->assertSame(10500.0, (float) $order->total_amount);

        // Stok langsung berkurang sesuai alokasi FEFO.
        $this->assertSame(17, $this->batch->fresh()->stock);
    }

    public function test_internal_staff_can_confirm_or_cancel_online_order(): void
    {
        $order = $this->makeOnlineOrder(2);

        $this->actingAs($this->admin)
            ->post(route('orders.mark-paid', $order))
            ->assertRedirect();

        $this->assertSame('paid', $order->fresh()->payment_status);

        $orderToCancel = $this->makeOnlineOrder(4);
        $this->assertSame(14, $this->batch->fresh()->stock);

        $this->actingAs($this->admin)
            ->post(route('orders.cancel', $orderToCancel))
            ->assertRedirect();

        $this->assertSame('cancelled', $orderToCancel->fresh()->payment_status);
        $this->assertSame(18, $this->batch->fresh()->stock);
    }

    public function test_notification_message_contains_order_details_and_total(): void
    {
        config()->set('services.telegram.bot_token', null);
        config()->set('services.telegram.chat_id', null);

        $order = $this->makeOnlineOrder(2);

        $message = app(PaymentNotifier::class)->buildMessage(
            $order,
            '[ PESANAN BARU ]',
            'Menunggu konfirmasi (pending)'
        );

        $this->assertStringContainsString('[ PESANAN BARU ]', $message);
        $this->assertStringContainsString('Invoice     : '.$order->invoice_number, $message);
        $this->assertStringContainsString('Status      : Menunggu konfirmasi (pending)', $message);
        $this->assertStringContainsString('Pembayaran  : COD', $message);
        $this->assertStringContainsString('RINCIAN PESANAN', $message);
        $this->assertStringContainsString('- Paracetamol 500mg', $message);
        $this->assertStringContainsString('2 Strip x Rp 3.500 = Rp 7.000', $message);
        $this->assertStringContainsString('TOTAL       Rp 7.000', $message);
        $this->assertStringContainsString(str_repeat('-', 20), $message);
        $this->assertStringContainsString('Terima kasih sudah berbelanja.', $message);
        $this->assertSame(0, preg_match('/[^\x00-\x7F]/', $message), 'Pesan tidak boleh bergantung pada glyph/font emoji.');
    }

    public function test_order_detail_has_primary_whatsapp_and_https_telegram_bot(): void
    {
        config()->set('services.telegram.bot_token', null);
        config()->set('services.telegram.chat_id', null);

        $order = $this->makeOnlineOrder(1);

        $response = $this->get(route('portal.order.show', $order->invoice_number));

        $response->assertOk();
        $response->assertSee('wa.me/6287773845477?text=', false);
        $response->assertDontSee('6281928310718', false);
        $response->assertSee('0877-7384-5477');
        $response->assertSee('btn-wa-portal', false);
        $response->assertSee('https://t.me/pharmacy_payments_bot', false);
        $response->assertDontSee('tg://resolve', false);
        $response->assertDontSee('%EF%BF%BD', false); // tidak ada karakter rusak (U+FFFD)
        $response->assertSee('RINCIAN+PESANAN', false);
        $response->assertSee('Rp 3.500');
    }

    public function test_checkout_uses_primary_whatsapp_and_https_telegram_bot(): void
    {
        $response = $this->get(route('portal.checkout'));

        $response->assertOk();
        $response->assertSee('wa.me/6287773845477', false);
        $response->assertDontSee('6281928310718', false);
        $response->assertSee('0877-7384-5477');
        $response->assertSee('btn-wa-portal', false);
        $response->assertSee('https://t.me/pharmacy_payments_bot', false);
        $response->assertDontSee('tg://resolve', false);
    }

    private function makeOnlineOrder(int $quantity): SalesTransaction
    {
        $user = User::create([
            'name'     => 'Sistem',
            'email'    => 'system' . uniqid() . '@apotek.com',
            'password' => 'password123',
            'role'     => 'admin',
        ]);

        $order = SalesTransaction::create([
            'user_id'        => $user->id,
            'invoice_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
            'order_channel'  => 'online',
            'total_amount'   => 3500 * $quantity,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'delivery_method'=> 'pickup',
            'recipient_name' => 'Pelanggan Uji',
            'recipient_phone'=> '081234567890',
        ]);

        $order->details()->create([
            'product_batch_id' => $this->batch->id,
            'quantity'         => $quantity,
            'price'            => 3500,
            'subtotal'         => 3500 * $quantity,
        ]);

        $this->batch->decrement('stock', $quantity);

        return $order;
    }
}
