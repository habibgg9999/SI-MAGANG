<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'type',
        'field',
        'city',
        'quota',
        'occupied_slots',
        'contact_person',
        'contact_phone',
        'is_active',
        'is_approved',
        'requested_by',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Relasi ke user pengusul instansi (jika diusulkan oleh mahasiswa).
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Relasi ke pendaftaran magang yang ditujukan ke instansi ini.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class, 'company_id');
    }

    /**
     * Scope instansi aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope instansi yang sudah disetujui koordinator.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope berdasarkan tipe instansi.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope eager calculate kuota terisi & pending via subquery.
     */
    public function scopeWithQuotaCounts(Builder $query): Builder
    {
        return $query
            ->select('companies.*')
            ->selectSub(function ($sub) {
                $sub->selectRaw('COALESCE(COUNT(gm.id), 0)')
                    ->from('group_members as gm')
                    ->join('internship_applications as ia', 'ia.group_id', '=', 'gm.group_id')
                    ->whereColumn('ia.company_id', 'companies.id')
                    ->where('ia.status', 'diterima')
                    ->where('gm.status', 'aktif');
            }, 'accepted_members_count')
            ->selectSub(function ($sub) {
                $sub->selectRaw('COALESCE(COUNT(gm.id), 0)')
                    ->from('group_members as gm')
                    ->join('internship_applications as ia', 'ia.group_id', '=', 'gm.group_id')
                    ->whereColumn('ia.company_id', 'companies.id')
                    ->where('ia.status', 'menunggu')
                    ->where('gm.status', 'aktif');
            }, 'pending_members_count');
    }

    /**
     * Scope filter pencarian multi-kriteria.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('field', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
            });
        });

        $query->when($filters['type'] ?? null, function ($q, $type) {
            if ($type !== 'all' && in_array($type, ['pemerintah', 'swasta', 'BUMN', 'NGO'])) {
                $q->where('type', $type);
            }
        });

        $query->when($filters['city'] ?? null, function ($q, $city) {
            if ($city !== 'all') {
                $q->where('city', $city);
            }
        });

        $query->when($filters['field'] ?? null, function ($q, $field) {
            if ($field !== 'all') {
                $q->where('field', 'like', "%{$field}%");
            }
        });

        return $query;
    }

    /**
     * Hitung jumlah mahasiswa dari kelompok yang status pengajuannya 'diterima'.
     */
    public function getAcceptedMembersCountAttribute(): int
    {
        if (array_key_exists('accepted_members_count', $this->attributes)) {
            return (int) $this->attributes['accepted_members_count'];
        }

        return (int) GroupMember::where('status', 'aktif')
            ->whereIn('group_id', function ($query) {
                $query->select('group_id')
                    ->from('internship_applications')
                    ->where('company_id', $this->id)
                    ->where('status', 'diterima');
            })
            ->count();
    }

    /**
     * Hitung jumlah mahasiswa dari kelompok yang pengajuannya masih 'menunggu'.
     */
    public function getPendingMembersCountAttribute(): int
    {
        if (array_key_exists('pending_members_count', $this->attributes)) {
            return (int) $this->attributes['pending_members_count'];
        }

        return (int) GroupMember::where('status', 'aktif')
            ->whereIn('group_id', function ($query) {
                $query->select('group_id')
                    ->from('internship_applications')
                    ->where('company_id', $this->id)
                    ->where('status', 'menunggu');
            })
            ->count();
    }

    /**
     * Sisa kuota yang belum terisi.
     */
    public function getRemainingQuotaAttribute(): int
    {
        return max(0, (int) $this->quota - $this->accepted_members_count);
    }

    /**
     * Persentase keterisian kuota (0 - 100%).
     */
    public function getQuotaPercentageAttribute(): float
    {
        if ($this->quota <= 0) {
            return $this->accepted_members_count > 0 ? 100.0 : 0.0;
        }

        $pct = ($this->accepted_members_count / $this->quota) * 100;
        return (float) min(100, round($pct, 1));
    }

    /**
     * Status Traffic Light Radar Kuota:
     * - 🟢 Hijau (< 50%) -> Aman
     * - 🟡 Kuning (50% - 89%) -> Terbatas
     * - 🔴 Merah (>= 90%) -> Kritis / Penuh
     */
    public function getQuotaStatusAttribute(): array
    {
        $pct = $this->quota_percentage;

        if ($pct < 50) {
            return [
                'level' => 'green',
                'label' => 'Aman',
                'sublabel' => 'Peluang Terbuka Lebar',
                'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'badge_pill' => 'bg-emerald-500/10 text-emerald-700 border-emerald-300',
                'bar_color' => 'bg-emerald-500',
                'dot_color' => 'bg-emerald-500',
                'ring_color' => 'ring-emerald-400',
                'icon' => '🟢',
                'description' => 'Keterisian di bawah 50%. Masih banyak slot kuota kosong.',
            ];
        } elseif ($pct >= 50 && $pct < 90) {
            return [
                'level' => 'yellow',
                'label' => 'Terbatas',
                'sublabel' => 'Kuota Menipis',
                'badge_bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'badge_pill' => 'bg-amber-500/10 text-amber-700 border-amber-300',
                'bar_color' => 'bg-amber-500',
                'dot_color' => 'bg-amber-500',
                'ring_color' => 'ring-amber-400',
                'icon' => '🟡',
                'description' => 'Keterisian 50% - 89%. Segera daftarkan kelompok Anda sebelum penuh.',
            ];
        } else {
            return [
                'level' => 'red',
                'label' => 'Kritis / Penuh',
                'sublabel' => 'Kuota Hampir/Sudah Habis',
                'badge_bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'badge_pill' => 'bg-rose-500/10 text-rose-700 border-rose-300',
                'bar_color' => 'bg-rose-500',
                'dot_color' => 'bg-rose-500',
                'ring_color' => 'ring-rose-400',
                'icon' => '🔴',
                'description' => 'Keterisian 90% ke atas. Persaingan ketat atau kuota telah terisi penuh.',
            ];
        }
    }

    /**
     * Badge visual traffic light ringkas untuk tabel & dashboard.
     */
    public function getTrafficBadgeAttribute(): array
    {
        $status = $this->quota_status;
        return [
            'indicator' => $status['icon'],
            'label' => $status['label'],
            'class' => $status['badge_bg'],
        ];
    }
}
