<?php

namespace App\States\Application;

class AcceptedState extends ApplicationState
{
    public function name(): string
    {
        return 'diterima';
    }

    public function label(): string
    {
        return 'Diterima';
    }

    public function allowedTransitions(): array
    {
        // Terminal State: Immutable
        return [];
    }
}
