<?php

namespace App\Console\Commands;

use App\Mail\ApplicationReminderMail;
use App\Models\InternshipApplication;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendApplicationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simagang:send-reminders {--force : Paksa kirim meskipun pengingat sudah pernah dikirim hari ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat otomatis kepada ketua kelompok yang pengajuan magangnya >= 14 hari berstatus menunggu.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan pengajuan magang menggantung (>= 14 hari)...');

        $threshold = Carbon::now()->subDays(14);

        $query = InternshipApplication::where('status', 'menunggu')
            ->where('applied_at', '<=', $threshold)
            ->with(['group.leader', 'company']);

        if (!$this->option('force')) {
            // Celah 10: Anti-Spam Email - Hanya kirim jika belum pernah diingatkan ATAU sudah >= 7 hari sejak reminder terakhir
            $query->where(function ($q) {
                $q->whereNull('deadline_reminder')
                  ->orWhere('deadline_reminder', '<=', Carbon::now()->subDays(7));
            });
        }

        $applications = $query->get();

        if ($applications->isEmpty()) {
            $this->info('Tidak ada permohonan magang berstatus "menunggu" yang mencapai batas 14 hari tanpa respon.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$applications->count()} permohonan magang yang memerlukan pengingat.");

        $tableData = [];
        $sentCount = 0;
        $failedCount = 0;

        foreach ($applications as $app) {
            $group = $app->group;
            $leader = $group ? $group->leader : null;
            $company = $app->company;

            if (!$leader || !$leader->email) {
                $this->warn("Aplikasi #{$app->id} (Kelompok: {$group?->name}): Ketua kelompok atau email tidak ditemukan.");
                $failedCount++;
                continue;
            }

            $daysWaiting = $app->applied_at ? (int) $app->applied_at->diffInDays(now()) : 14;

            try {
                // Outbox Pattern (Pilar 5): Catat ke system_outbox secara transaksional
                \App\Models\SystemOutbox::record('send_application_reminder', [
                    'application_id' => $app->id,
                    'email' => $leader->email,
                    'group_name' => $group->name,
                    'leader_name' => $leader->name,
                ]);

                // Kirim email Mailable ke ketua kelompok
                Mail::to($leader->email)->send(new ApplicationReminderMail($app));

                // Perbarui timestamp reminder di database
                $app->update([
                    'deadline_reminder' => Carbon::now(),
                ]);

                $sentCount++;

                $tableData[] = [
                    'Kelompok' => $group->name,
                    'Ketua' => $leader->name,
                    'Email' => $leader->email,
                    'Instansi' => $company ? $company->name : '-',
                    'Diajukan' => $app->applied_at ? $app->applied_at->format('d/m/Y') : '-',
                    'Tertunda' => "{$daysWaiting} hari",
                    'Status' => 'Terkirim',
                ];
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("Gagal mengirim email ke {$leader->email}: {$e->getMessage()}");
                Log::error("Gagal mengirim pengingat magang untuk Aplikasi #{$app->id}: " . $e->getMessage());
            }
        }

        if (!empty($tableData)) {
            $this->table(
                ['Kelompok', 'Ketua', 'Email', 'Instansi Tujuan', 'Tgl Diajukan', 'Tertunda', 'Status'],
                $tableData
            );
        }

        $this->info("Proses selesai. Berhasil: {$sentCount}, Gagal: {$failedCount}.");

        return Command::SUCCESS;
    }
}
