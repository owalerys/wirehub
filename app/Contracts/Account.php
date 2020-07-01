<?php

namespace App\Contracts;

interface Account extends HasUniversalIdentifier
{
    public function getName(): string;
    public function getNickname(): ?string;

    public function getType(): ?string;

    public function getCurrencyCode(): string;

    public function getBalances(): object;
    public function getNumbers(): object;

    public function getMask(): string;

    public function isDepository(): bool;

    public function getParentResourceIdentifier(): string;
}
