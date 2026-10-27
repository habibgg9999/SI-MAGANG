<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemOutbox extends Model
{
    use HasFactory;

    protected $table = 'system_outbox';

    protected $fillable = [
        'event_type',
        'payload',
        'status', // pending, processing, processed, failed
        'retry_count',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'retry_count' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Catat event baru ke outbox table secara transaksional.
     */
    public static function record(string $eventType, array $payload): self
    {
        return self::create([
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => 'pending',
            'retry_count' => 0,
        ]);
    }
}
