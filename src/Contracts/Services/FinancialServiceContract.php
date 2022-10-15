<?php
namespace Spork\Finance\Contracts\Services;

use Spork\Core\Models\FeatureList;
use Carbon\Carbon;

interface FinancialServiceContract
{
    public function getTransactions(FeatureList $accessToken, Carbon $startDate, Carbon $endDate): array;
    public function getAccounts(FeatureList $accessToken): array;
}
