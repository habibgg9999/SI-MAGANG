<?php

namespace App\Console\Commands;

use App\Mail\ApplicationReminderMail;
use App\Models\InternshipApplication;
use App\Models\SystemOutbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessSystemOutbox extends Command
{
    protected $signature = 'simagang:process-outbox {--limit=50 : Batas jumlah pesan outbox yang diproses}';

    protected $description = 'Worker pemroses Transactional Outbox Pattern untuk pengiriman email & asynchronous events.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Memproses antrean Transactional Outbox (Limit: {$limit})...");

        $pendingEvents = SystemOutbox::where('status', 'pending')
            ->where('retry_count', '<', 3)
            ->oldest()
            ->limit($limit)
            ->get();

        if ($pendingEvents->isEmpty()) {
            $this->info('Tidak ada pesan tertunda di antrean system_outbox.');
            return Command::SUCCESS;
        }

        $processed = 0;
        $failed = 0;

        foreach ($pendingEvents as $event) {
            $event->update(['status' => 'processing']);

            try {
                $this->processEvent($event);

                $event->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
                $processed++;
            } catch (\Throwable $e) {
                $retries = $event->retry_count + 1;
                $newStatus = ($retries >= 3) ? 'failed' : 'pending';

                $event->update([
                    'status' => $newStatus,
                    'retry_count' => $retries,
                    'error_message' => $e->getMessage(),
                ]);

                $failed++;
                Log::error("Gagal memproses Outbox Event #{$event->id} ({$event->event_type}): " . $e->getMessage());
            }
        }

        $this->info("Outbox processing selesai. Berhasil: {$processed}, Gagal: {$failed}.");

        return Command::SUCCESS;
    }

    /**
     * Eksekusi payload berdasarkan tipe event.
     */
    protected function processEvent(SystemOutbox $event): void
    {
        switch ($event->event_type) {
            case 'send_application_reminder':
                $applicationId = $event->payload['application_id'] ?? null;
                $email = $event->payload['email'] ?? null;

                if ($applicationId && $email) {
                    $application = InternshipApplication::with(['group.leader', 'company'])->find($applicationId);
                    if ($application) {
                        Mail::to($email)->send(new ApplicationReminderMail($application));
                    }
                }
                break;

            default:
                Log::info("Unhandled outbox event type: {$event->event_type}");
                break;
        }
    }
}
