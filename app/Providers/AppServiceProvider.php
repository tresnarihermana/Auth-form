<?php

namespace App\Providers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
          Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
        return app(NoCaptcha::class)->verifyResponse($value);
        
    });
     Gate::before(function ($user, $ability) {
        return $user->hasRole('Super Admin') ? true : null;
    });
    }
}
