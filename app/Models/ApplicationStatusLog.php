<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ApplicationStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'status_before',
        'status_after',
        'changed_by',
        'actor_id',
        'ip_address',
        'user_agent',
        'reason',
        'changed_at',
        'hash_signature',
        'microtime',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    /**
     * Catat mutasi status dengan rantai hash kriptografis HMAC SHA-256 (Tamper-Evident).
     */
    public static function recordMutation(
        InternshipApplication $application,
        ?string $statusBefore,
        string $statusAfter,
        ?User $actor = null,
        ?string $reason = null
    ): self {
        $actorId = $actor ? $actor->id : Auth::id();
        $ip = request()->ip() ?? '127.0.0.1';
        $userAgent = substr(request()->userAgent() ?? 'CLI/System', 0, 500);
        $microtime = sprintf('%.6f', microtime(true));

        // Ambil hash baris log terakhir milik aplikasi ini
        $lastLog = self::where('application_id', $application->id)->latest('id')->first();
        $previousHash = $lastLog?->hash_signature ?? str_repeat('0', 64);

        // Bentuk payload rantai kriptografis
        $payload = "{$previousHash}|{$application->id}|" . ($statusBefore ?? 'NONE') . "|{$statusAfter}|" . ($actorId ?? '0') . "|{$ip}|{$microtime}";
        $secretKey = config('app.key') ?: 'simagang-cryptographic-audit-key';
        $signature = hash_hmac('sha256', $payload, $secretKey);

        return self::create([
            'application_id' => $application->id,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'changed_by' => $actorId,
            'actor_id' => $actorId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'reason' => $reason,
            'changed_at' => now(),
            'hash_signature' => $signature,
            'microtime' => $microtime,
        ]);
    }

    /**
     * Verifikasi integritas kriptografis rantai log audit (Deteksi Tampering).
     * Mengembalikan TRUE jika tidak ada manipulasi, FALSE jika database diubah langsung via SQL.
     */
    public static function verifyIntegrity(InternshipApplication|int $application): bool
    {
        $appId = $application instanceof InternshipApplication ? $application->id : $application;
        $logs = self::where('application_id', $appId)->orderBy('id', 'asc')->get();
        if ($logs->isEmpty()) {
            return true;
        }

        $secretKey = config('app.key') ?: 'simagang-cryptographic-audit-key';
        $previousHash = str_repeat('0', 64);

        foreach ($logs as $log) {
            $statusBefore = $log->status_before ?? 'NONE';
            $actorId = $log->actor_id ?? '0';
            $payload = "{$previousHash}|{$appId}|{$statusBefore}|{$log->status_after}|{$actorId}|{$log->ip_address}|{$log->microtime}";
            $expectedSignature = hash_hmac('sha256', $payload, $secretKey);

            if ($log->hash_signature !== $expectedSignature) {
                return false; // Tampering detected!
            }

            $previousHash = $log->hash_signature;
        }

        return true;
    }

    /**
     * Relasi ke pendaftaran magang terkait.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(InternshipApplication::class, 'application_id');
    }

    /**
     * Relasi ke pengguna (koordinator/mahasiswa) yang melakukan perubahan status.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
