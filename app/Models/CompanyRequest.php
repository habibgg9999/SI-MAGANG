<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'name',
        'address',
        'type',
        'field',
        'city',
        'quota_estimate',
        'contact_person',
        'status',
        'reject_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'quota_estimate' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke mahasiswa yang mengajukan usulan instansi.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Relasi ke koordinator yang meninjau usulan instansi.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope pengajuan berstatus pending.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pengajuan yang disetujui.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope pengajuan yang ditolak.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }
}
