<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Siapa saja (mahasiswa / koordinator / tamu) boleh melihat daftar kelompok.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Siapa saja boleh melihat profil kelompok.
     */
    public function view(?User $user, Group $group): bool
    {
        return true;
    }

    /**
     * Hanya mahasiswa yang belum punya kelompok aktif yang boleh membuat kelompok.
     */
    public function create(User $user): bool
    {
        return $user->isMahasiswa() && !$user->hasActiveGroup();
    }

    /**
     * Hanya ketua kelompok yang boleh mengupdate profil kelompok.
     */
    public function update(User $user, Group $group): bool
    {
        return $user->id === $group->leader_id || $user->isKoordinator();
    }

    /**
     * Hanya ketua kelompok yang boleh mengelola (tambah/keluarkan) anggota.
     */
    public function manageMembers(User $user, Group $group): bool
    {
        return $user->id === $group->leader_id;
    }

    /**
     * Hanya ketua kelompok yang boleh mendaftarkan magang.
     */
    public function apply(User $user, Group $group): bool
    {
        return $user->id === $group->leader_id;
    }

    /**
     * Mahasiswa boleh bergabung jika belum punya kelompok aktif, bukan anggota, dan kuota tersedia.
     */
    public function join(User $user, Group $group): bool
    {
        return $user->isMahasiswa() 
            && !$user->hasActiveGroup() 
            && $group->canAcceptMoreMembers()
            && !$group->hasAcceptedApplication();
    }
}
