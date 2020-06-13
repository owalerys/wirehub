<?php

namespace App\Concerns;

use App\Services\Discovery;

trait IsFlinks
{
    protected function getProvider(): string
    {
        return Discovery::PROVIDER_FLINKS;
    }
}
