<?php

namespace Tests\Feature;

use App\Services\RiansaKartuService;
use Mockery;
use Tests\TestCase;

class KartuWebhookSecurityTest extends TestCase
{
    public function test_missing_wrong_and_malformed_secrets_are_rejected_without_processing(): void
    {
        $service = Mockery::mock(RiansaKartuService::class);
        $service->shouldReceive('getWebhookSecret')->andReturn('test-webhook-secret');
        $service->shouldNotReceive('processWebhookPayload');
        $this->app->instance(RiansaKartuService::class, $service);

        foreach (['/api/kartu/webhook', '/api/v1/kartu/webhook'] as $url) {
            $this->postJson($url, [])->assertUnauthorized();
            $this->postJson($url, [], ['X-Webhook-Secret' => 'wrong'])->assertUnauthorized();
            $this->postJson($url, ['secret' => ['malformed']])->assertUnauthorized();
        }
    }

    public function test_unconfigured_webhook_fails_closed(): void
    {
        $service = Mockery::mock(RiansaKartuService::class);
        $service->shouldReceive('getWebhookSecret')->andReturn(null);
        $service->shouldNotReceive('processWebhookPayload');
        $this->app->instance(RiansaKartuService::class, $service);
        $this->postJson('/api/v1/kartu/webhook')->assertStatus(503);
    }

    public function test_both_headers_and_legacy_body_secret_accept_valid_requests_without_forwarding_secret(): void
    {
        $service = Mockery::mock(RiansaKartuService::class);
        $service->shouldReceive('getWebhookSecret')->andReturn('test-webhook-secret');
        $service->shouldReceive('processWebhookPayload')->times(3)->with(['event' => 'attendance'])
            ->andReturn(['success' => true, 'message' => 'Accepted']);
        $this->app->instance(RiansaKartuService::class, $service);

        foreach (['X-Webhook-Secret', 'X-Kartu-Secret'] as $header) {
            $this->postJson('/api/v1/kartu/webhook', ['event' => 'attendance'], [$header => 'test-webhook-secret'])->assertOk();
        }
        $this->postJson('/api/kartu/webhook', ['event' => 'attendance', 'secret' => 'test-webhook-secret'])->assertOk();
    }

    public function test_valid_authentication_preserves_payload_validation_errors(): void
    {
        $service = Mockery::mock(RiansaKartuService::class);
        $service->shouldReceive('getWebhookSecret')->andReturn('test-webhook-secret');
        $service->shouldReceive('processWebhookPayload')->once()->andReturn(['success' => false, 'message' => 'Invalid payload']);
        $this->app->instance(RiansaKartuService::class, $service);
        $this->postJson('/api/v1/kartu/webhook', [], ['X-Webhook-Secret' => 'test-webhook-secret'])->assertStatus(400);
    }
}
