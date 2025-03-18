<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::tokensExpireIn(now()->addDays(15));

        Passport::enablePasswordGrant();
    }
}
