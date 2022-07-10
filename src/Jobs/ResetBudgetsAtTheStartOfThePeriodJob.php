<?php

namespace Spork\Finance\Jobs;

use App\Models\FeatureList;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetBudgetsAtTheStartOfThePeriodJob implements ShouldQueue
{
    public function handle()
    {
        // Bill 
        $budgets = FeatureList::forFeature(FeatureList::FEATURE_BUDGETS)
        // Schedule
            ->with('repeatables')
            ->get();

        foreach ($budgets as $budget) {
            $budget->settings->last_reset_at = now();
            $budget->settings->is_pending = false;
            $budget->settings->total_spend = 0.0;
            $budget->settings->paid = false;
            $budget->settings->exceeded_spends_at = null;
            
            $budget->save();
        }
    }
}