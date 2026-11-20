<?php

namespace Tests\Unit;

use App\Services\PaymentNotifier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentNotifierTest extends TestCase
{
    public function test_notification_is_skipped_when_telegram_config_is_missing(): void
    {
        Http::fake();
        config()->set('services.telegram.bot_token', null);
        config()->set('services.telegram.chat_id', null);

        $result = app(PaymentNotifier::class)->send('Pesan uji');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_failed_telegram_response_does_not_throw(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => false], 401),
        ]);
        config()->set('services.telegram.bot_token', 'dummy-token');
        config()->set('services.telegram.chat_id', '12345');

        $result = app(PaymentNotifier::class)->send('Pesan uji');

        $this->assertFalse($result);
    }
}
