<?php

namespace Spork\Finance\Services;

use App\Models\FeatureList;
use Spork\Finance\Contracts\Services\PlaidServiceContract;
use Spork\Finance\Models\Account;
use Carbon\Carbon;
use Spork\Finance\Events\AccountsUpdateEvent;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\Plaid;

class PlaidService implements PlaidServiceContract
{
    protected Plaid $service ;
    public function __construct() {
        $this->service = new Plaid(
            env("PLAID_CLIENT_ID"),
            env("PLAID_CLIENT_SECRET"),
            env("PLAID_ENVIRONMENT")
        );
    }

    public function createLinkToken()
    {
        $user = new User(auth()->id(), auth()->user()->name, null, null, auth()->user()->email);

        return $this->service->tokens->create('special-fiesta', 'en', ['US'], $user, ['transactions'], 'https://7eb4-24-231-174-107.ngrok.io/api/plaid/webhook');
    }

    public function exchangeLinkTokenForAccessToken($token)
    {
        return $this->service->items->exchangeToken($token);
    }

    public function getTransactions(FeatureList $token, Carbon $startDate, Carbon $endDate): array
    {
        $accessToken = $token->settings['access_token'];

        $transactions = $this->service->transactions->list(
            $accessToken,
            $startDate,
            $endDate
        );

        $accounts = [];
        foreach ($transactions->accounts as $account) {

            $acct = $token->accounts()->where('account_id', $account->account_id)->first();

            if (empty($acct)) {
                $acct = $token->accounts()->create([
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'subtype' => $account->subtype,
                    'balance' => $account->balances->current,
                    'available' => $account->balances->available,
                    'mask' => $account->mask,
                ]);
                $accounts[$acct->account_id] = $acct;
            } else {
                $acct->update([
                    'name' => $account->name,
                    'type' => $account->type,
                    'subtype' => $account->subtype,
                    'balance' => $account->balances->current,
                    'available' => $account->balances->available,
                    'mask' => $account->mask,
                ]);
                $accounts[$acct->account_id] = $acct->refresh();
            }
        }

        $updatedTransactions = array_map(function ($transaction) use ($accounts) {
            /** @var Account $account */
            $account = $accounts[$transaction->account_id];

            $localTransaction = $account->transactions()->firstWhere('transaction_id', $transaction->transaction_id);

            if (empty($localTransaction)) {
                $localTransaction = $account->transactions()->create([
                    'name' => $transaction->name,
                    'amount' => $transaction->amount,
                    'account_id' => $transaction->account_id,
                    'date' => $transaction->date,
                    'pending' => $transaction->pending,
                    'category_id' => $transaction->category_id,
                    'transaction_id' => $transaction->transaction_id,
                ]);
            }

            $localTransaction->update([
                'name' => $transaction->name,
                'amount' => $transaction->amount,
                'account_id' => $transaction->account_id,
                'date' => $transaction->date,
                'pending' => $transaction->pending,
                'category_id' => $transaction->category_id,
                'transaction_id' => $transaction->transaction_id,
            ]);

            return $localTransaction;
        }, $transactions->transactions);

        return $updatedTransactions;
    }

    public function getAccounts(FeatureList $accessToken): array
    {
        $accessToken = $accessToken->settings->access_token;
        return [];
    }
}
