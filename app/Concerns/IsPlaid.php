<?php

namespace App\Concerns;

use App\Services\Discovery;

trait IsPlaid
{
    protected function getProvider(): string
    {
        return Discovery::PROVIDER_PLAID;
    }
}
