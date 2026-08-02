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
    ) {
    }

    /**
     * Handle incoming webhook POST from Riansa Kartu.
     * Endpoint: POST /api/v1/kartu/webhook
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('RiansaKartu Webhook Received:', $request->all());

        // Optional Secret Verification if Webhook Secret is set
        $secret = $this->kartuService->getWebhookSecret();
        if ($secret) {
            $headerSecret = $request->header('X-Webhook-Secret') ?? $request->header('X-Kartu-Secret') ?? $request->input('secret');
            if ($headerSecret && !hash_equals((string) $secret, (string) $headerSecret)) {
                Log::warning('RiansaKartu Webhook Secret Mismatch.');
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid webhook secret token',
                ], 401);
            }
        }

        $payload = $request->all();
        $result = $this->kartuService->processWebhookPayload($payload);

        if (!$result['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $result['message'],
        ], 200);
    }
}
