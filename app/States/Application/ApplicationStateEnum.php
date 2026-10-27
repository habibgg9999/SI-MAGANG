<?php

namespace App\States\Application;

enum ApplicationStateEnum: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'menunggu';
    case UNDER_REVIEW = 'under_review';
    case ACCEPTED = 'diterima';
    case REJECTED = 'ditolak';
    case WITHDRAWN = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Menunggu Konfirmasi',
            self::UNDER_REVIEW => 'Sedang Diproses Instansi',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
            self::WITHDRAWN => 'Dibatalkan',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED, self::WITHDRAWN]);
    }
}
