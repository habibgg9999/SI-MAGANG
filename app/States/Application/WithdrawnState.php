<?php

namespace App\States\Application;

class WithdrawnState extends ApplicationState
{
    public function name(): string
    {
        return 'dibatalkan';
    }

    public function label(): string
    {
        return 'Dibatalkan';
    }

    public function allowedTransitions(): array
    {
        // Terminal State: Immutable
        return [];
    }
}
