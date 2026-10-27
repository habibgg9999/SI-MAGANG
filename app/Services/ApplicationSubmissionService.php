<?php

namespace App\Services;

use App\Exceptions\IllegalStateTransitionException;
use App\Exceptions\QuotaExceededException;
use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationSubmissionService
{
    /**
     * Ajukan pendaftaran magang dengan Distributed Locking, DB Transaction Retry, dan Idempotency Engine.
     */
    public function submitApplication(
        int $groupId,
        int $companyId,
        ?string $notes,
        User $actor,
        ?string $idempotencyKey = null
    ): InternshipApplication {
        // 1. Idempotency Check (Pilar 2): Cegah duplicate submission saat network timeout / double-click
        if ($idempotencyKey) {
            $cachedAppId = Cache::get("idempotency_app_submit:{$idempotencyKey}");
            if ($cachedAppId) {
                return InternshipApplication::with(['group', 'company'])->findOrFail($cachedAppId);
            }
        }

        // 2. Distributed Cache Lock (Pilar 2)
        $lockKey = "company_quota_lock_{$companyId}";
        $lock = Cache::lock($lockKey, 10);

        return $lock->block(5, function () use ($groupId, $companyId, $notes, $actor, $idempotencyKey) {
            // 3. Database Transaction dengan Retry hingga 5x saat deadlock
            return DB::transaction(function () use ($groupId, $companyId, $notes, $actor, $idempotencyKey) {
                // Pessimistic Row Locking pada Group dan Company
                $group = Group::where('id', $groupId)->lockForUpdate()->firstOrFail();
                $company = Company::where('id', $companyId)->lockForUpdate()->firstOrFail();

                // Validasi Invariant: Perusahaan harus aktif & disetujui
                if (!$company->is_active || !$company->is_approved) {
                    throw ValidationException::withMessages([
                        'company_id' => 'Instansi tujuan tidak ditemukan, belum disetujui, atau sedang non-aktif.',
                    ]);
                }

                // Validasi Invariant: Kelompok tidak boleh punya pengajuan 'menunggu'
                $hasPending = InternshipApplication::where('group_id', $group->id)
                    ->where('status', 'menunggu')
                    ->exists();

                if ($hasPending) {
                    throw ValidationException::withMessages([
                        'group_id' => 'Kelompok masih memiliki permohonan magang berstatus MENUNGGU. Tidak dapat mendaftar ganda.',
                    ]);
                }

                // Validasi Invariant: Kelompok tidak boleh sudah diterima di tempat lain
                $hasAccepted = InternshipApplication::where('group_id', $group->id)
                    ->where('status', 'diterima')
                    ->exists();

                if ($hasAccepted) {
                    throw ValidationException::withMessages([
                        'group_id' => 'Kelompok sudah berstatus DITERIMA di instansi lain.',
                    ]);
                }

                // Validasi Duplikasi di instansi yang sama
                $existing = InternshipApplication::where('group_id', $group->id)
                    ->where('company_id', $company->id)
                    ->whereIn('status', ['menunggu', 'diterima'])
                    ->exists();

                if ($existing) {
                    throw ValidationException::withMessages([
                        'company_id' => "Kelompok sudah memiliki permohonan aktif di instansi ini.",
                    ]);
                }

                $isRed = $company->quota_status['level'] === 'red';
                $warningNote = $isRed ? ' [Pengajuan pada instansi dengan indikator MERAH / Kuota Kritis]' : '';

                $application = InternshipApplication::create([
                    'group_id' => $group->id,
                    'company_id' => $company->id,
                    'status' => 'menunggu',
                    'applied_at' => now(),
                    'responded_at' => null,
                    'notes' => ($notes ?? '') . $warningNote,
                    'deadline_reminder' => now()->addDays(14),
                ]);

                // Catat mutasi awal dengan rantai kriptografis
                ApplicationStatusLog::recordMutation(
                    application: $application,
                    statusBefore: null,
                    statusAfter: 'menunggu',
                    actor: $actor,
                    reason: 'Pengajuan proposal pendaftaran magang awal oleh Ketua Kelompok (' . $actor->name . ')' . $warningNote
                );

                // Simpan Idempotency Key selama 120 detik (Pilar 2)
                if ($idempotencyKey) {
                    Cache::put("idempotency_app_submit:{$idempotencyKey}", $application->id, 120);
                }

                return $application;
            }, 5);
        });
    }

    /**
     * Konfirmasi penerimaan / penolakan status magang dengan FSM, Pessimistic Locking, dan Concurrency Guard.
     *
     * @throws QuotaExceededException|IllegalStateTransitionException
     */
    public function updateApplicationStatus(
        InternshipApplication $application,
        string $targetStatus,
        ?string $reason,
        User $actor,
        ?string $idempotencyKey = null
    ): InternshipApplication {
        // 1. Idempotency Check (Pilar 2)
        if ($idempotencyKey) {
            $cachedStatus = Cache::get("idempotency_app_status:{$idempotencyKey}");
            if ($cachedStatus) {
                return $application->refresh();
            }
        }

        // 2. Distributed Cache Lock pada Kuota Instansi
        $lockKey = "company_quota_lock_{$application->company_id}";
        $lock = Cache::lock($lockKey, 10);

        return $lock->block(5, function () use ($application, $targetStatus, $reason, $actor, $idempotencyKey) {
            // 3. Database Transaction dengan Retry hingga 5x
            return DB::transaction(function () use ($application, $targetStatus, $reason, $actor, $idempotencyKey) {
                $lockedApp = InternshipApplication::where('id', $application->id)->lockForUpdate()->firstOrFail();
                $lockedCompany = Company::where('id', $lockedApp->company_id)->lockForUpdate()->firstOrFail();

                // FSM Validation (Pilar 1): Validasi transisi status formal
                if (!$lockedApp->state()->canTransitionTo($targetStatus)) {
                    throw new IllegalStateTransitionException(
                        fromState: $lockedApp->status,
                        toState: $targetStatus,
                        applicationId: $lockedApp->id,
                        actorId: $actor->id
                    );
                }

                // Advanced Concurrency Guard (Pilar 2): Jika status diubah ke 'diterima', cek kuota ketat
                if ($targetStatus === 'diterima') {
                    // Hitung total mahasiswa aktif yang sudah berstatus 'diterima' di instansi ini
                    $currentAcceptedMembers = GroupMember::where('status', 'aktif')
                        ->whereIn('group_id', function ($query) use ($lockedCompany) {
                            $query->select('group_id')
                                ->from('internship_applications')
                                ->where('company_id', $lockedCompany->id)
                                ->where('status', 'diterima');
                        })
                        ->count();

                    $incomingMembers = $lockedApp->group->activeMembers()->count();

                    // Cek apakah melampaui kuota resmi
                    if (($currentAcceptedMembers + $incomingMembers) > $lockedCompany->quota) {
                        throw new QuotaExceededException(
                            companyId: $lockedCompany->id,
                            quota: $lockedCompany->quota,
                            attemptedCount: ($currentAcceptedMembers + $incomingMembers),
                            message: "Kuota instansi '{$lockedCompany->name}' ({$lockedCompany->quota} slot) tidak mencukupi untuk menerima kelompok ini (memerlukan {$incomingMembers} slot, tersisa " . max(0, $lockedCompany->quota - $currentAcceptedMembers) . " slot)."
                        );
                    }

                    // Sinkronisasi Read-Projection CQRS Lite kolom occupied_slots (Pilar 3)
                    $lockedCompany->update([
                        'occupied_slots' => $currentAcceptedMembers + $incomingMembers,
                    ]);
                } elseif ($lockedApp->status === 'diterima' && in_array($targetStatus, ['dibatalkan', 'ditolak'])) {
                    // Jika sebelumnya diterima dan sekarang dibatalkan, kurangi read-projection
                    $incomingMembers = $lockedApp->group->activeMembers()->count();
                    $lockedCompany->decrement('occupied_slots', min($lockedCompany->occupied_slots, $incomingMembers));
                }

                // Eksekusi mutasi status via State Pattern Engine
                $updatedApp = $lockedApp->transitionTo($targetStatus, $actor, $reason);

                // Simpan Idempotency Key (120 detik)
                if ($idempotencyKey) {
                    Cache::put("idempotency_app_status:{$idempotencyKey}", $targetStatus, 120);
                }

                return $updatedApp;
            }, 5);
        });
    }
}
