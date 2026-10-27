<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_id',
        'max_members',
        'is_open',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_members' => 'integer',
            'is_open' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke ketua kelompok (User).
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Relasi ke semua record pivot anggota kelompok.
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    /**
     * Relasi ke record anggota kelompok yang berstatus aktif.
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id')->where('status', 'aktif');
    }

    /**
     * Relasi Many-to-Many ke User melalui tabel group_members.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')
            ->withPivot(['id', 'status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Relasi Many-to-Many ke User yang berstatus aktif.
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivot('status', 'aktif');
    }

    /**
     * Relasi ke riwayat pendaftaran magang yang diajukan oleh kelompok.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class, 'group_id');
    }

    /**
     * Relasi ke pengajuan magang terbaru dari kelompok.
     */
    public function latestApplication(): HasOne
    {
        return $this->hasOne(InternshipApplication::class, 'group_id')->latestOfMany();
    }

    /**
     * Relasi ke postingan forum yang ditautkan ke kelompok ini.
     */
    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'group_id');
    }

    /**
     * Cek apakah kuota kelompok sudah penuh.
     */
    public function isFull(): bool
    {
        return $this->activeMembers()->count() >= $this->max_members;
    }

    /**
     * Scope kelompok yang masih membuka anggota baru.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true)->where('is_active', true);
    }

    /**
     * Scope kelompok aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Cek apakah kelompok masih bisa menerima anggota baru.
     */
    public function canAcceptMoreMembers(): bool
    {
        return $this->is_open && $this->is_active && !$this->isFull();
    }

    /**
     * Cek apakah kelompok memiliki pengajuan yang masih berstatus 'menunggu'.
     */
    public function hasPendingApplication(): bool
    {
        return $this->applications()->where('status', 'menunggu')->exists();
    }

    /**
     * Dapatkan pengajuan yang sedang berstatus 'menunggu'.
     */
    public function pendingApplication(): ?InternshipApplication
    {
        return $this->applications()->where('status', 'menunggu')->with('company')->first();
    }

    /**
     * Cek apakah kelompok sudah memiliki pengajuan yang 'diterima'.
     */
    public function hasAcceptedApplication(): bool
    {
        return $this->applications()->where('status', 'diterima')->exists();
    }

    /**
     * Dapatkan pengajuan yang berstatus 'diterima'.
     */
    public function acceptedApplication(): ?InternshipApplication
    {
        return $this->applications()->where('status', 'diterima')->with('company')->first();
    }
}
