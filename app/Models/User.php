<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nim',
        'name',
        'email',
        'phone',
        'prodi',
        'angkatan',
        'role',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => 'encrypted',
        ];
    }

    /**
     * Mutator Pengaman: Mencegah Mass Assignment & Privilege Escalation pada field 'role'.
     * Mahasiswa tidak dapat mengubah role-nya menjadi 'koordinator' via request tampering.
     */
    public function setRoleAttribute($value): void
    {
        // Hanya izinkan pengaturan role jika berjalan di console/seeder atau dilakukan oleh Koordinator resmi
        if (app()->runningInConsole() || (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isKoordinator())) {
            $this->attributes['role'] = $value;
        } elseif (!isset($this->attributes['role'])) {
            // Default pembuatan akun awal
            $this->attributes['role'] = in_array($value, ['mahasiswa', 'koordinator']) ? $value : 'mahasiswa';
        }
        // Jika user mahasiswa mencoba mengubah role saat sudah login, aksi diabaikan secara diam-diam (silent drop)
    }

    /**
     * Relasi ke kelompok yang dipimpin oleh user ini.
     */
    public function ledGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    /**
     * Relasi ke data keanggotaan kelompok (pivot model).
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    /**
     * Relasi many-to-many ke kelompok magang yang diikuti.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')
            ->withPivot(['id', 'status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Relasi kelompok dengan status aktif.
     */
    public function activeGroups(): BelongsToMany
    {
        return $this->groups()->wherePivot('status', 'aktif');
    }

    /**
     * Relasi ke postingan forum yang dibuat oleh user ini.
     */
    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'user_id');
    }

    /**
     * Relasi ke pengajuan instansi baru yang diajukan user (mahasiswa).
     */
    public function companyRequests(): HasMany
    {
        return $this->hasMany(CompanyRequest::class, 'requested_by');
    }

    /**
     * Relasi ke pengajuan instansi yang ditinjau oleh user (koordinator).
     */
    public function reviewedCompanyRequests(): HasMany
    {
        return $this->hasMany(CompanyRequest::class, 'reviewed_by');
    }

    /**
     * Relasi ke perusahaan yang ditambahkan atas usulan user.
     */
    public function requestedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'requested_by');
    }

    /**
     * Relasi ke riwayat perubahan status pendaftaran magang yang dilakukan user.
     */
    public function applicationStatusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class, 'changed_by');
    }

    /**
     * Cek apakah role koordinator.
     */
    public function isKoordinator(): bool
    {
        return $this->role === 'koordinator';
    }

    /**
     * Cek apakah role mahasiswa.
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Dapatkan kelompok aktif saat ini (jika ada).
     */
    public function currentActiveGroup(): ?Group
    {
        return $this->activeGroups()->where('groups.is_active', true)->first();
    }

    /**
     * Cek apakah user sudah tergabung dalam suatu kelompok aktif.
     */
    public function hasActiveGroup(): bool
    {
        return $this->currentActiveGroup() !== null;
    }
}
