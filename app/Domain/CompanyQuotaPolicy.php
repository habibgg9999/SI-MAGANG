<?php

namespace App\Domain;

use App\Exceptions\QuotaExceededException;
use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;

/**
 * Domain Policy: CompanyQuotaPolicy
 *
 * Mengelola Read-Projection CQRS-Lite kolom `occupied_slots` pada tabel `companies`.
 * Mencegah degradasi performa dari count(*) dinamis saat traffic tinggi serentak.
 */
class CompanyQuotaPolicy
{
    /**
     * Pastikan instansi memiliki sisa kuota yang mencukupi untuk menerima kelompok.
     *
     * @throws QuotaExceededException
     */
    public static function assertCanAccept(Company $company, Group $group): void
    {
        $incomingMembers = $group->activeMembers()->count();
        $occupied = $company->occupied_slots;
        $totalQuota = $company->quota;

        if (($occupied + $incomingMembers) > $totalQuota) {
            throw new QuotaExceededException(
                companyId: $company->id,
                quota: $totalQuota,
                attemptedCount: ($occupied + $incomingMembers),
                message: "Kuota instansi '{$company->name}' ({$totalQuota} slot) tidak mencukupi untuk menerima kelompok '{$group->name}' ({$incomingMembers} mahasiswa). Sisa kuota: " . max(0, $totalQuota - $occupied) . " slot."
            );
        }
    }

    /**
     * Tingkatkan alokasi kuota terisi secara atomic.
     */
    public static function allocateSlots(Company $company, int $slots): void
    {
        $company->increment('occupied_slots', $slots);
    }

    /**
     * Kurangi alokasi kuota terisi secara atomic (saat pengajuan dibatalkan atau ditolak).
     */
    public static function releaseSlots(Company $company, int $slots): void
    {
        if ($slots <= 0) {
            return;
        }

        $current = $company->occupied_slots;
        $decreaseBy = min($current, $slots);

        if ($decreaseBy > 0) {
            $company->decrement('occupied_slots', $decreaseBy);
        }
    }

    /**
     * Rekonsiliasi sinkronisasi read-projection dari data riil untuk mencegah data drift.
     */
    public static function reconcile(Company $company): int
    {
        $realCount = GroupMember::where('status', 'aktif')
            ->whereIn('group_id', function ($query) use ($company) {
                $query->select('group_id')
                    ->from('internship_applications')
                    ->where('company_id', $company->id)
                    ->where('status', 'diterima');
            })
            ->count();

        $company->update(['occupied_slots' => $realCount]);

        return $realCount;
    }
}
