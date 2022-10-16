<?php

namespace Spork\Finance\Jobs;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spork\Core\Models\FeatureList;
use Spork\Finance\Events\AccountUpdateRequested;

class RequestRefreshAllAccountsJob
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function handle()
    {
        $features = FeatureList::forFeature('finance')->get();

        $features->each(function ($feature) {
            event(new AccountUpdateRequested($feature));
        });
    }
}
