<?php

namespace Spork\Finance\Services;

use Carbon\Carbon;
use Spork\Core\Models\FeatureList;
use Spork\Finance\Contracts\Services\PlaidServiceContract;

class CsvService implements PlaidServiceContract
{
    public function getTransactions(FeatureList $accessToken, Carbon $startDate, Carbon $endDate): array
    {
        $transactionFile = $accessToken->settings->file;

        return [];
    }

    public function getAccounts(FeatureList $accessToken): array
    {
        return [];
    }
}
