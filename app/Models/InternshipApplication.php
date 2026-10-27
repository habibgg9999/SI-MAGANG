<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InternshipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'company_id',
        'status',
        'applied_at',
        'responded_at',
        'notes',
        'deadline_reminder',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'responded_at' => 'datetime',
            'deadline_reminder' => 'datetime',
        ];
    }

    /**
     * Relasi ke kelompok yang mendaftar.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Relasi ke instansi tujuan magang.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relasi ke log histori perubahan status.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class, 'application_id');
    }

    /**
     * Relasi ke log perubahan status terakhir.
     */
    public function latestStatusLog(): HasOne
    {
        return $this->hasOne(ApplicationStatusLog::class, 'application_id')->latestOfMany('changed_at');
    }

    /**
     * Scope pengajuan yang masih menunggu konfirmasi.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'menunggu');
    }

    /**
     * Scope pengajuan yang diterima.
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'diterima');
    }

    /**
     * Scope pengajuan yang ditolak.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Scope pengajuan yang dibatalkan.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'dibatalkan');
    }

    /**
     * Dapatkan objek FSM State saat ini.
     */
    public function state(): \App\States\Application\ApplicationState
    {
        return \App\States\Application\ApplicationState::fromApplication($this);
    }

    /**
     * Lakukan transisi status via State Pattern Engine.
     */
    public function transitionTo(string $targetState, ?User $actor = null, ?string $reason = null): self
    {
        return $this->state()->transitionTo($targetState, $actor, $reason);
    }
}

