<?php

namespace App\Contracts;

interface Transaction extends HasUniversalIdentifier
{
    public function getNumericalID(): int;

    public function getName(): string;
    public function getAmount(): string;
    public function getDate(): string;

    public function getParentResourceIdentifier(): string;

    public function getConfirmed(): ?bool;
    public function getConfirmedAt(): ?string;

    public function confirm(bool $value);

    /**
     * Flinks specific, for now
     */
    public function getBalance(): ?string;

    /**
     * Plaid specific, for now
     */
    public function getCategory(): ?array;
    public function getTransactionType(): ?string;
    public function getCurrencyCode(): ?string;
    public function getPending(): ?bool;
    public function getAuthorizedDate(): ?string;
    public function getLocation(): ?object;
    public function getPaymentMeta(): ?object;
    public function getPaymentChannel(): ?string;
    public function getTransactionCode(): ?string;
}
