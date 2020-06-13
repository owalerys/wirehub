<?php

namespace App\Contracts;

interface Item extends HasUniversalIdentifier
{
    public function getInstitutionName(): string;

    public function getInstitutionMeta(): ?object;

    public function disconnect();

    /**
     * Force refresh data
     */
    public function eagerRefresh();

    /**
     * Detail Refresh (Transactions)
     */
    public function detailRefresh(bool $fullHistory = false);

    /**
     * Summary Refresh (Accounts)
     */
    public function summaryRefresh();
}
