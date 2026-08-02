<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use App\Services\QrAttendanceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RiansaKartuService
{
    private string $baseUrl;

    public function __construct(
        private readonly QrAttendanceService $qrAttendance
    ) {
        $this->baseUrl = config('kartu.base_url', 'https://riansa.top/kartu/v1');
    }

    public function isEnabled(): bool
    {
        return (bool) Setting::get('kartu_enabled', false);
    }

    public function getApiKey(): ?string
    {
        return Setting::get('kartu_api_key');
    }

    public function getWebhookSecret(): ?string
    {
        return Setting::get('kartu_webhook_secret');
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Push student list (members) to Riansa Kartu API.
     * Endpoint: POST https://riansa.top/kartu/v1/members.php
     */
    public function syncMembers(?Collection $students = null): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return ['success' => false, 'message' => 'API Key Riansa Kartu belum dikonfigurasi.'];
        }

        $students ??= Student::with('class')->get();

        if ($students->isEmpty()) {
            return ['success' => false, 'message' => 'Tidak ada data siswa untuk disinkronkan.'];
        }

        $items = $students->map(function (Student $student) {
            $ident = (string) ($student->nisn ?: $student->nis ?: $student->id);
            return [
                'ident'  => $ident,
                'name'   => $student->name,
                'role'   => 'siswa',
                'grup'   => $student->class?->name ?? 'Tanpa Kelas',
                'active' => (bool) $student->status,
            ];
        })->values()->toArray();

        $chunks = array_chunk($items, 250);
        $totalSynced = 0;

        foreach ($chunks as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type'  => 'application/json',
                ])->timeout(30)->post($this->baseUrl . '/members.php', [
                    'items' => $chunk,
                ]);

                if ($response->successful()) {
                    $totalSynced += count($chunk);
                } else {
                    Log::error('RiansaKartu syncMembers error: ' . $response->body());
                    return [
                        'success' => false,
                        'message' => 'Gagal mengirim data ke Riansa Kartu: ' . ($response->json('message') ?? $response->status()),
                        'synced'  => $totalSynced,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('RiansaKartu syncMembers exception: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage(),
                    'synced'  => $totalSynced,
                ];
            }
        }

        Setting::set('kartu_last_synced_at', now()->toDateTimeString(), 'kartu');

        return [
            'success' => true,
            'message' => "Berhasil menyinkronkan {$totalSynced} data siswa ke Riansa Kartu.",
            'synced'  => $totalSynced,
        ];
    }

    /**
     * Pull attendance logs from Riansa Kartu API using cursor (since).
     * Endpoint: GET https://riansa.top/kartu/v1/attendance.php?since={cursor}&limit=200
     */
    public function pullAttendance(): array
    {
        $apiKey = $this->getApiKey();
        if (!$apiKey) {
            return ['success' => false, 'message' => 'API Key Riansa Kartu belum dikonfigurasi.'];
        }

        $since = Setting::get('kartu_last_cursor', '0');
        if (empty($since)) {
            $since = '0';
        }

        $url = $this->baseUrl . '/attendance.php?since=' . urlencode($since) . '&limit=200';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($apiKey),
            ])->timeout(30)->get($url);

            if (!$response->successful()) {
                Log::error('RiansaKartu pullAttendance error: ' . $response->body());
                return ['success' => false, 'message' => 'Gagal menarik absensi dari Riansa Kartu: HTTP ' . $response->status()];
            }

            $data = $response->json();
            $items = $data['items'] ?? $data['data'] ?? (is_array($data) ? $data : []);
            $nextCursor = $data['cursor'] ?? $data['next_cursor'] ?? null;

            $recordedCount = 0;
            foreach ($items as $item) {
                $ident = $item['ident'] ?? $item['nisn'] ?? $item['nis'] ?? $item['student_id'] ?? null;
                if (!$ident) {
                    continue;
                }

                $student = Student::with('class')
                    ->where('nisn', $ident)
                    ->orWhere('nis', $ident)
                    ->orWhere('id', $ident)
                    ->first();

                if ($student) {
                    $result = $this->qrAttendance->recordAttendance($student);
                    if ($result['success']) {
                        $recordedCount++;
                    }
                }
            }

            if ($nextCursor) {
                Setting::set('kartu_last_cursor', $nextCursor, 'kartu');
            }

            return [
                'success' => true,
                'message' => "Berhasil memproses " . count($items) . " log absensi ({$recordedCount} presensi baru dicatat).",
                'synced_count' => $recordedCount,
                'next_cursor' => $nextCursor,
            ];
        } catch (\Exception $e) {
            Log::error('RiansaKartu pullAttendance exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan koneksi: ' . $e->getMessage()];
        }
    }

    /**
     * Process real-time Webhook event from Riansa Kartu tap machine.
     */
    public function processWebhookPayload(array $payload): array
    {
        $ident = $payload['ident'] ?? $payload['nisn'] ?? $payload['nis'] ?? $payload['student_id'] ?? $payload['code'] ?? null;

        if (!$ident) {
            return ['success' => false, 'message' => 'Identitas siswa (ident/nisn/nis) tidak ditemukan dalam payload webhook.'];
        }

        $student = Student::with('class')
            ->where('nisn', $ident)
            ->orWhere('nis', $ident)
            ->orWhere('id', $ident)
            ->first();

        if (!$student) {
            return ['success' => false, 'message' => "Siswa dengan ident/NISN '{$ident}' tidak ditemukan di LMS."];
        }

        $result = $this->qrAttendance->recordAttendance($student);

        Log::info("RiansaKartu Webhook processed for Student NISN {$ident}: " . json_encode($result));

        return $result;
    }
}

