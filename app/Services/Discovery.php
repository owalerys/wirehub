<?php

namespace App\Services;

use App\Contracts\Account as ContractsAccount;
use App\Contracts\HasUniversalIdentifier;
use App\Flinks\Account as FlinksAccount;
use App\Flinks\Item as FlinksItem;
use App\Plaid\Account as PlaidAccount;
use App\Plaid\Item as PlaidItem;
use App\Plaid\Transaction as PlaidTransaction;
use App\Flinks\Transaction as FlinksTransaction;
use Illuminate\Support\Collection;

class Discovery
{
    const PROVIDER_PLAID = 'plaid';
    const PROVIDER_FLINKS = 'flinks';
    const PROVIDER_BACKRUB = 'backrub';

    const TYPE_ACCOUNT = 'account';
    const TYPE_ITEM = 'item';
    const TYPE_TRANSACTION = 'transaction';

    public function identifyCollection(array $identifiers): Collection
    {
        $output = array_map(function ($identifier) {
            if ($item = $this->identifyItem($identifier)) return $item;
            if ($account = $this->identifyAccount($identifier)) return $account;
            return null;
        }, $identifiers);

        $filtered = array_filter($output, function ($x) {
            return $x !== null;
        });

        $collection = collect($filtered);

        return self::keyUniversal($collection);
    }

    public static function keyUniversal(Collection $collection): Collection
    {
        return $collection->keyBy(function (HasUniversalIdentifier $element) {
            return $element->getResourceIdentifier();
        });
    }

    public function allAccounts(): Collection
    {
        $output = collect();

        /**
         * PLAID
         */
        $plaidAccounts = PlaidAccount::with('item')->get();
        $output = $output->merge(self::keyUniversal($plaidAccounts));

        /**
         * FLINKS
         */
        $flinksAccounts = FlinksAccount::with('item')->get();
        $output = $output->merge(self::keyUniversal($flinksAccounts));

        return $output;
    }

    public function getAllItems(): Collection
    {
        $output = collect();

        /**
         * PLAID
         */
        $plaidItems = PlaidItem::all();
        $output = $output->merge(self::keyUniversal($plaidItems));

        /**
         * FLINKS
         */
        $flinksItems = FlinksItem::all();
        $output = $output->merge(self::keyUniversal($flinksItems));

        return $output;
    }

    public function getAllItemsForRefresh(): Collection
    {
        $output = collect();

        /**
         * PLAID
         */
        $plaidItems = PlaidItem::has('accounts.teams')->with('accounts.teams')->get();
        $output = $output->merge(self::keyUniversal($plaidItems));

        /**
         * FLINKS
         */
        $flinksItems = FlinksItem::has('accounts.teams')->with('accounts.teams')->get();
        $output = $output->merge(self::keyUniversal($flinksItems));

        return $output;
    }

    public function identifyItem(string $identifier)
    {
        if (!$this->validateIdentifier($identifier)) return;

        if ($this->getType($identifier) !== self::TYPE_ITEM) return;

        $provider = $this->getProvider($identifier);

        if (!$provider) return;

        $uuid = $this->getIdentifier($identifier);

        if ($provider === self::PROVIDER_PLAID) return PlaidItem::where('external_id', $uuid)->first();
        if ($provider === self::PROVIDER_FLINKS) return FlinksItem::where('id', $uuid)->first();
    }

    public function identifyAccount(string $identifier)
    {
        if (!$this->validateIdentifier($identifier)) return;

        if ($this->getType($identifier) !== self::TYPE_ACCOUNT) return;

        $provider = $this->getProvider($identifier);

        if (!$provider) return;

        $uuid = $this->getIdentifier($identifier);

        if ($provider === self::PROVIDER_PLAID) return PlaidAccount::where('external_id', $uuid)->first();
        if ($provider === self::PROVIDER_FLINKS) return FlinksAccount::where('external_id', $uuid)->first();
    }

    public function identifyTransaction(string $identifier)
    {
        if (!$this->validateIdentifier($identifier)) return;

        if ($this->getType($identifier) !== self::TYPE_TRANSACTION) return;

        $provider = $this->getProvider($identifier);

        if (!$provider) return;

        $uuid = $this->getIdentifier($identifier);

        if ($provider === self::PROVIDER_PLAID) return PlaidTransaction::where('external_id', $uuid)->first();
        if ($provider === self::PROVIDER_FLINKS) return FlinksTransaction::where('external_id', $uuid)->first();
    }

    private function validateIdentifier(string $identifier): bool
    {
        return count(explode(':', $identifier)) === 3;
    }

    private function getProvider(string $identifier): ?string
    {
        $split = explode(':', $identifier);

        $provider = $split[0];

        if ($provider === self::trimParameter(self::PROVIDER_PLAID)) return self::PROVIDER_PLAID;
        if ($provider === self::trimParameter(self::PROVIDER_FLINKS)) return self::PROVIDER_FLINKS;
    }

    private function getType(string $identifier): ?string
    {
        $split = explode(':', $identifier);

        $type = $split[1];

        if ($type === self::trimParameter(self::TYPE_TRANSACTION)) return self::TYPE_TRANSACTION;
        if ($type === self::trimParameter(self::TYPE_ACCOUNT)) return self::TYPE_ACCOUNT;
        if ($type === self::trimParameter(self::TYPE_ITEM)) return self::TYPE_ITEM;
    }

    private function getIdentifier(string $identifier): ?string
    {
        $split = explode(':', $identifier);

        $uuid = $split[2];

        if ($uuid) return $uuid;
    }

    public static function createIdentifier(string $provider, string $type, string $uuid): string
    {
        return join(':', [
            strtolower(self::trimParameter($provider)),
            strtolower(self::trimParameter($type)),
            $uuid
        ]);
    }

    private static function trimParameter(string $parameter)
    {
        return substr($parameter, 0, 2);
    }
}
