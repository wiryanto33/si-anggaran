<?php

namespace App\Providers;

use App\Models\AnnualBudgetItem;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Observers\AnnualBudgetItemObserver;
use App\Observers\ProposalItemObserver;
use App\Observers\ProposalObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        ProposalItem::observe(ProposalItemObserver::class);
        AnnualBudgetItem::observe(AnnualBudgetItemObserver::class);
        Proposal::observe(ProposalObserver::class);
    }
}
