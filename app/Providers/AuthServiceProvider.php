<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //Passport::routes();

        Passport::tokensCan([
            'manage-branch' => 'Manage branch',
            'manage-user' => 'Add ECCSM POS user',
            'delete-transactions' => 'Delete saved transactions',
            'purchase-items' => 'Purchase item from supplier to branch',
        ]);

        // Passport::tokensExpireIn(Carbon::now()->addHours(1));
        // Passport::refreshTokensExpireIn(Carbon::createFromTime(0,0,0)->addDays(1));
    }
}
