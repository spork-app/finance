<?php

namespace Spork\Finance\Contracts\Services;

use Carbon\Carbon;
use Spork\Core\Models\FeatureList;

interface FinancialServiceContract
{
    public function getTransactions(FeatureList $accessToken, Carbon $startDate, Carbon $endDate): array;

    public function getAccounts(FeatureList $accessToken): array;
}
