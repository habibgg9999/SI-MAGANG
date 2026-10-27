<?php

namespace App\Policies;

use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Koordinator atau anggota kelompok yang bersangkutan dapat melihat detail pengajuan.
     */
    public function view(User $user, InternshipApplication $application): bool
    {
        if ($user->isKoordinator()) {
            return true;
        }

        return $application->group->activeMembers()->where('user_id', $user->id)->exists();
    }

    /**
     * Hanya Ketua Kelompok (leader_id) yang berhak mengajukan pendaftaran magang.
     */
    public function create(User $user, int $groupId): bool
    {
        $group = \App\Models\Group::find($groupId);
        if (!$group) {
            return false;
        }

        return $user->id === $group->leader_id;
    }

    /**
     * Hanya Ketua Kelompok pemilik data atau Koordinator Magang yang dapat memperbarui status pengajuan.
     */
    public function updateStatus(User $user, InternshipApplication $application): bool
    {
        if ($user->isKoordinator()) {
            return true;
        }

        return $user->id === $application->group->leader_id;
    }

    /**
     * Hanya Koordinator yang berhak menghapus data pengajuan magang.
     */
    public function delete(User $user, InternshipApplication $application): bool
    {
        return $user->isKoordinator();
    }
}
