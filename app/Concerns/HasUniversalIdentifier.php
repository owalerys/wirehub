<?php

namespace App\Concerns;

use App\Services\Discovery;

trait HasUniversalIdentifier {

    protected abstract function getUniqueId(): string;
    protected abstract function getProvider(): string;
    protected abstract function getModelType(): string;

    /**
     * Get an identifier
     * Plaid | Account | UUID
     *
     * pl:ac:abcdabcd-abcd-abcd-abcd-abcdabcdabcd
     *
     * @return string
     */
    public function getResourceIdentifier(): string
    {
        return Discovery::createIdentifier(
            $this->getProvider(),
            $this->getModelType(),
            $this->getUniqueId()
        );
    }

}
