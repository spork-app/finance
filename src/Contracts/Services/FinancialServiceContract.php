<?php
namespace Spork\Finance\Contracts\Services;

use App\Models\FeatureList;
use Carbon\Carbon;

interface FinancialServiceContract
{
    public function getTransactions(FeatureList $accessToken, Carbon $startDate, Carbon $endDate): array;
    public function getAccounts(FeatureList $accessToken): array;
}
