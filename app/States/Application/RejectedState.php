<?php

namespace App\States\Application;

class RejectedState extends ApplicationState
{
    public function name(): string
    {
        return 'ditolak';
    }

    public function label(): string
    {
        return 'Ditolak';
    }

    public function allowedTransitions(): array
    {
        // Terminal State: Immutable
        return [];
    }
}
