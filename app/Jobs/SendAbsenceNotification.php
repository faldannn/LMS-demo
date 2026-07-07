<?php

namespace App\Jobs;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{Http, Log};

class SendAbsenceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly int $studentId,
        public readonly string $date,
    ) {
    }

    public function handle(): void
    {
        $student = Student::with('class')->find($this->studentId);
        if (!$student || !$student->parent_phone)
            return;

        $phone = preg_replace('/^0/', '62', $student->parent_phone);
        $tanggal = \Carbon\Carbon::parse($this->date)->translatedFormat('l, d F Y');

        $message = "Yth. Bapak/Ibu {$student->parent_name},\n\n"
            . "Putra/Putri Anda atas nama *{$student->name}* "
            . "(Kelas {$student->class?->name})\n"
            . "tercatat *TIDAK HADIR (ALFA)* pada:\n"
            . "📅 {$tanggal}\n\n"
            . "Mohon menghubungi wali kelas untuk keterangan lebih lanjut.\n\n"
            . "Terima kasih.\n"
            . "– " . config('school.name', 'LMS');

        try {
            $response = Http::timeout(15)
                ->withToken(config('services.fonnte.token'))
                ->post(config('services.fonnte.url', 'https://api.fonnte.com/send'), [
                    'target' => $phone,
                    'message' => $message,
                ]);

            $result = $response->json();
            if (!$response->successful() || (isset($result['status']) && $result['status'] === false)) {
                Log::warning("WA notification failed for student {$this->studentId}", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                throw new \Exception("Fonnte API Error: " . ($result['reason'] ?? 'Unknown error'));
            }
            
            Log::info("WA notification sent successfully to {$phone}");
        } catch (\Exception $e) {
            Log::error("WA notification exception: " . $e->getMessage());
            throw $e; // Allow retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("WA notification permanently failed for student {$this->studentId} after {$this->tries} attempts. Error: {$exception->getMessage()}");
    }
}