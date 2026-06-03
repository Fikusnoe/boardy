<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use Laravel\Passport\Passport;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\Http\Responses\SimpleViewResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Laravel\Passport\Contracts\AuthorizationViewResponse::class, function ($app) {
            return new \Laravel\Passport\Http\Responses\SimpleViewResponse('passport::authorize');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
	User::observe(UserObserver::class);
	
	Passport::useClientModel(\App\Models\PassportClient::class);

	if (!$this->app->bound(\Laravel\Passport\Contracts\AuthorizationViewResponse::class)) {
            \Log::error('DEBUG: Passport bindings are missing! PassportServiceProvider is not loaded.');
        } else {
            \Log::info('DEBUG: Passport bindings ARE present.');
        }
    }
}
