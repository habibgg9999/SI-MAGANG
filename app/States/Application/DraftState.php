<?php

namespace App\States\Application;

class DraftState extends ApplicationState
{
    public function name(): string
    {
        return 'draft';
    }

    public function label(): string
    {
        return 'Draft';
    }

    public function allowedTransitions(): array
    {
        return ['menunggu'];
    }
}
