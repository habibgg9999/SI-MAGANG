<?php

namespace App\Models;

use App\Helpers\WhatsappHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'type',
        'title',
        'body',
        'target_company',
        'field',
        'slots_needed',
        'is_fulfilled',
    ];

    protected function casts(): array
    {
        return [
            'slots_needed' => 'integer',
            'is_fulfilled' => 'boolean',
        ];
    }

    /**
     * Relasi ke pembuat postingan (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke kelompok terkait (opsional/nullable jika tipe cari_kelompok).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Scope postingan pencarian anggota.
     */
    public function scopeCariAnggota(Builder $query): Builder
    {
        return $query->where('type', 'cari_anggota');
    }

    /**
     * Scope postingan pencarian kelompok.
     */
    public function scopeCariKelompok(Builder $query): Builder
    {
        return $query->where('type', 'cari_kelompok');
    }

    /**
     * Scope postingan yang belum terpenuhi / masih open.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_fulfilled', false);
    }

    /**
     * Scope filter pencarian forum.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhere('target_company', 'like', "%{$search}%")
                    ->orWhere('field', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('prodi', 'like', "%{$search}%");
                    });
            });
        });

        $query->when($filters['type'] ?? null, function ($q, $type) {
            if (in_array($type, ['cari_anggota', 'cari_kelompok'])) {
                $q->where('type', $type);
            }
        });

        $query->when($filters['field'] ?? null, function ($q, $field) {
            if ($field !== 'all' && !empty($field)) {
                $q->where('field', 'like', "%{$field}%");
            }
        });

        $query->when(isset($filters['status']) && $filters['status'] === 'open', function ($q) {
            $q->where('is_fulfilled', false);
        });

        return $query;
    }

    /**
     * Accessor untuk link WhatsApp terintegrasi.
     * Format: https://wa.me/{nomor_hp}?text={pesan_otomatis}
     * Pesan: "Halo [Nama], saya melihat postingan Anda di SIMAGANG mengenai [Judul Post]..."
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $author = $this->user;
        if (!$author || !$author->phone) {
            return null;
        }

        return WhatsappHelper::generateLink(
            $author->phone,
            $author->name,
            $this->title
        );
    }

    /**
     * Hitung sisa slot riil kelompok jika postingan tipe cari_anggota.
     */
    public function getRemainingGroupSlotsAttribute(): int
    {
        if ($this->type === 'cari_anggota' && $this->group) {
            $group = $this->group;
            $activeCount = $group->activeMembers()->count();
            return max(0, $group->max_members - $activeCount);
        }

        return $this->slots_needed ?? 1;
    }
}
