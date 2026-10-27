<?php

namespace App\Domain;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Domain Aggregate Root: GroupAggregate
 * 
 * Bertanggung jawab menjaga domain invariants keanggotaan kelompok:
 * 1. Kelompok belum penuh (current active members < max_members).
 * 2. Kelompok berstatus 'Open' (jika joining mandiri).
 * 3. Ghost Quota Protection: Anggota tidak boleh diubah jika kelompok sudah diterima di instansi.
 * 4. Phantom Member Protection: Mahasiswa dilarang memiliki lebih dari satu kelompok aktif secara bersamaan.
 * 5. Controller dilarang langsung memanggil GroupMember::create().
 */
class GroupAggregate
{
    public function __construct(protected Group $group)
    {
    }

    /**
     * Factory untuk membuat aggregate dari instance Group atau ID.
     */
    public static function for(Group|int $group): self
    {
        $groupModel = $group instanceof Group ? $group : Group::findOrFail($group);
        return new self($groupModel);
    }

    /**
     * Dapatkan instance Group terkait.
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * Tambahkan anggota ke dalam kelompok dengan pemenuhan Invariant Domain dan Pessimistic Locking.
     *
     * @throws ValidationException
     */
    public function addMember(User $user, bool $requireOpen = false): GroupMember
    {
        return DB::transaction(function () use ($user, $requireOpen) {
            // Pessimistic Row Lock pada Group dan User untuk mencegah race conditions
            $lockedGroup = Group::where('id', $this->group->id)->lockForUpdate()->firstOrFail();
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            // Invariant 1: Ghost Quota - Kelompok yang sudah diterima terkunci susunan anggotanya
            if ($lockedGroup->hasAcceptedApplication()) {
                throw ValidationException::withMessages([
                    'group' => "Kelompok '{$lockedGroup->name}' sudah berstatus DITERIMA di instansi magang. Susunan anggota terkunci demi integritas kuota resmi.",
                ]);
            }

            // Invariant 2: Recruitment status (bila dipersyaratkan misalnya saat join mandiri)
            if ($requireOpen && !$lockedGroup->is_open) {
                throw ValidationException::withMessages([
                    'group' => "Kelompok '{$lockedGroup->name}' sedang tidak membuka pendaftaran anggota baru.",
                ]);
            }

            // Invariant 3: Kapasitas maksimal kelompok
            if ($lockedGroup->isFull()) {
                throw ValidationException::withMessages([
                    'group' => "Kelompok sudah mencapai batas kuota maksimal ({$lockedGroup->max_members} anggota).",
                ]);
            }

            // Invariant 4: Anti-Phantom Member - Mahasiswa dilarang punya kelompok aktif lain
            if ($lockedUser->hasActiveGroup()) {
                $otherGroup = $lockedUser->currentActiveGroup();
                throw ValidationException::withMessages([
                    'user_id' => "Mahasiswa {$lockedUser->name} sudah tergabung dalam kelompok aktif lain ('{$otherGroup->name}').",
                ]);
            }

            // Cek apakah pernah bergabung sebelumnya (re-aktifasi) atau insert baru
            $memberRecord = GroupMember::where('group_id', $lockedGroup->id)
                ->where('user_id', $lockedUser->id)
                ->first();

            if ($memberRecord) {
                $memberRecord->update([
                    'status' => 'aktif',
                    'joined_at' => now(),
                ]);
                return $memberRecord;
            }

            return GroupMember::create([
                'group_id' => $lockedGroup->id,
                'user_id' => $lockedUser->id,
                'status' => 'aktif',
                'joined_at' => now(),
            ]);
        }, 5);
    }

    /**
     * Keluarkan anggota dari kelompok dengan proteksi Ghost Quota.
     *
     * @throws ValidationException
     */
    public function removeMember(User $user, User $actor): bool
    {
        $isLeader = $actor->id === $this->group->leader_id;
        $isSelf = $actor->id === $user->id;

        if (!$isLeader && !$isSelf) {
            throw ValidationException::withMessages([
                'authorization' => 'Anda tidak memiliki hak untuk mengeluarkan anggota ini.',
            ]);
        }

        if ($isSelf && $isLeader) {
            throw ValidationException::withMessages([
                'role' => 'Ketua Kelompok tidak dapat keluar dari kelompok yang dipimpinnya. Serahkan kepemimpinan terlebih dahulu.',
            ]);
        }

        return DB::transaction(function () use ($user) {
            $lockedGroup = Group::where('id', $this->group->id)->lockForUpdate()->firstOrFail();

            if ($lockedGroup->hasAcceptedApplication()) {
                throw ValidationException::withMessages([
                    'group' => 'Anggota tidak dapat dikeluarkan karena kelompok telah berstatus DITERIMA di instansi magang.',
                ]);
            }

            $member = GroupMember::where('group_id', $lockedGroup->id)
                ->where('user_id', $user->id)
                ->where('status', 'aktif')
                ->lockForUpdate()
                ->first();

            if (!$member) {
                throw ValidationException::withMessages([
                    'user_id' => 'Anggota tidak ditemukan dalam kelompok aktif.',
                ]);
            }

            return $member->update(['status' => 'keluar']);
        }, 5);
    }
}
