<?php

namespace App\Contracts;

interface HasUniversalIdentifier
{
    public function getResourceIdentifier(): string;
}
