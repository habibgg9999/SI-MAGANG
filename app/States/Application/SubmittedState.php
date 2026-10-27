<?php

namespace App\States\Application;

class SubmittedState extends ApplicationState
{
    public function name(): string
    {
        return 'menunggu';
    }

    public function label(): string
    {
        return 'Menunggu Konfirmasi';
    }

    public function allowedTransitions(): array
    {
        // Dari Submitted (menunggu), boleh ke: under_review, diterima, ditolak, dibatalkan
        return ['under_review', 'diterima', 'ditolak', 'dibatalkan'];
    }
}
