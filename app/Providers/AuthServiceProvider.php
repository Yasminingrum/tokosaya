<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        Gate::define('admin-access', function ($user) {
            return in_array($user->role_id, [1, 2]); // superadmin & admin
        });

        Gate::define('super-admin-access', function ($user) {
            return $user->role_id == 1; // superadmin only
        });
    }
}
