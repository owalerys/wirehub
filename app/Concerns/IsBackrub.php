<?php

namespace App\Concerns;

use App\Services\Discovery;

trait IsBackrub
{
    protected function getProvider(): string
    {
        return Discovery::PROVIDER_BACKRUB;
    }
}
