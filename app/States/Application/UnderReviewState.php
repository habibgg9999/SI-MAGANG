<?php

namespace App\States\Application;

class UnderReviewState extends ApplicationState
{
    public function name(): string
    {
        return 'under_review';
    }

    public function label(): string
    {
        return 'Sedang Diproses Instansi';
    }

    public function allowedTransitions(): array
    {
        // Dari UnderReview, boleh ke: diterima, ditolak, dibatalkan
        return ['diterima', 'ditolak', 'dibatalkan'];
    }
}
