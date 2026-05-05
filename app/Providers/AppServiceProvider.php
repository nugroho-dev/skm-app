<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use App\Providers\FortifyServiceProvider;
use App\Http\Responses\CustomLoginResponse;
use App\Http\Responses\UniformPasswordResetLinkResponse;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use App\Services\MenuService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(FortifyServiceProvider::class);
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
        $this->app->singleton(FailedPasswordResetLinkRequestResponse::class, UniformPasswordResetLinkResponse::class);
        $this->app->singleton(SuccessfulPasswordResetLinkRequestResponse::class, UniformPasswordResetLinkResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('public-grafik', function (Request $request) {
            $institutionFilter = $request->query('institution', $request->query('institution_id'));

            return [
                Limit::perMinute(30)->by($request->ip()),
                Limit::perMinute(45)->by($request->ip().'|'.($institutionFilter ?? 'all')),
            ];
        });

        RateLimiter::for('public-publikasi', function (Request $request) {
            $institutionFilter = $request->query('institution', $request->query('institution_id'));

            return [
                Limit::perMinute(8)->by($request->ip()),
                Limit::perMinute(12)->by($request->ip().'|'.($institutionFilter ?? 'all')),
            ];
        });

        View::composer('*', function ($view) {
        $user = Auth::user();

        $menus = [];

        if ($user) {
            $role = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null;
            $menus = MenuService::getMenu($role);
        }

        $view->with('menus', $menus);
        });
    
        
    }
}
