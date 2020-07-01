<?php

namespace App\Http\Controllers;

use App\Contracts\Transaction as ContractsTransaction;
use App\Http\Resources\Account as ResourcesAccount;
use App\Http\Resources\Transaction as ResourcesTransaction;
use App\Services\Discovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function getAccounts(Discovery $service)
    {
        if (!Gate::any(['view-any-accounts', 'view-own-accounts'])) {
            return abort(403);
        }

        if (Gate::allows('view-any-accounts')) {
            return response()->json(ResourcesAccount::collection($service->allAccounts()->values()));
        } elseif (Gate::allows('view-own-accounts')) {
            return response()->json(ResourcesAccount::collection(Auth::user()->team->getAllAccounts()->values()));
        }
    }

    public function getAccount(string $accountId, Discovery $service)
    {
        $account = $service->identifyAccount($accountId);

        if (!$account) return abort(404);

        Gate::authorize('view-account', $account);

        $account->load('item');
        $account->load('teams.users');

        return response()->json(new ResourcesAccount($account));
    }

    public function patchAccount(string $accountId, Discovery $service, Request $request)
    {
        $account = $service->identifyAccount($accountId);

        if (!$account) return abort(404);

        Gate::authorize('update-account', $account);

        $this->validate($request, [
            'nickname' => 'sometimes|string|nullable'
        ]);

        if ($request->has('nickname')) {
            $account->nickname = $request->input('nickname');
        }

        $account->save();

        return response()->json();
    }

    public function getAccountTransactions(string $accountId, Discovery $service)
    {
        $account = $service->identifyAccount($accountId);

        if (!$account) return abort(404);

        Gate::authorize('view-account', $account);

        $transactions = $account->transactions()->wire(Auth::user())->orderBy('date', 'desc')->limit(200)->get();

        return response()->json(ResourcesTransaction::collection($transactions));
    }

    public function getAccountTransactionsHistorical(string $accountId, Discovery $service)
    {
        /** @var App\Contracts\Account $account */
        $account = $service->identifyAccount($accountId);

        if (!$account) return abort(404);

        Gate::authorize('view-account', $account);

        $output = [];

        foreach($account->transactions()->wire(Auth::user())->orderBy('date', 'desc')->cursor() as $transaction) {
            /** @var ContractsTransaction $transaction */
            array_push($output, [
                'ID' => $transaction->getNumericalID(),
                'Date' => $transaction->getDate(),
                'Name' => $transaction->getName(),
                'Amount' => $transaction->getAmount(),
                'Pending' => $transaction->getPending() !== null ? ($transaction->getPending() ? 'Pending' : 'Settled') : 'N/A',
                'Currency' => $transaction->getCurrencyCode() ?: $account->getCurrencyCode(),
                'Credited To User' => $transaction->getConfirmed() ? 'Credited' : '',
                'Credited Changed At' => $transaction->getConfirmedAt() ?: ''
            ]);
        }

        return response()->json($output);
    }

    public function putTeamLink(string $accountId, Request $request, Discovery $service)
    {
        $this->validate($request, [
            'team_id' => 'required|exists:teams,id'
        ]);

        $account = $service->identifyAccount($accountId);

        if (!$account) return abort(404);

        Gate::authorize('update-account', $account);

        $account->teams()->sync([(int) $request->input('team_id')]);

        return response('', 200);
    }
}
