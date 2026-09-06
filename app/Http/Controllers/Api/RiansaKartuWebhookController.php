<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiansaKartuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiansaKartuWebhookController extends Controller
{
    public function __construct(
        private readonly RiansaKartuService $kartuService
    ) {}

    /**
     * Handle incoming webhook POST from Riansa Kartu.
     * Endpoint: POST /api/v1/kartu/webhook
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = $this->kartuService->getWebhookSecret();
        // Fail closed until both sides have a shared secret configured.
        if (! is_string($secret) || $secret === '') {
            Log::warning('RiansaKartu webhook rejected: secret is not configured.');

            return response()->json(['status' => 'error', 'message' => 'Webhook is not configured'], 503);
        }

        $headerSecret = $request->header('X-Webhook-Secret') ?? $request->header('X-Kartu-Secret') ?? $request->input('secret');
        if (! is_string($headerSecret) || $headerSecret === '' || ! hash_equals($secret, $headerSecret)) {
            Log::warning('RiansaKartu Webhook Secret Mismatch.');

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook secret token',
            ], 401);
        }

        $payload = $request->except('secret');
        $result = $this->kartuService->processWebhookPayload($payload);

        if (! $result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
        ], 200);
    }
}
