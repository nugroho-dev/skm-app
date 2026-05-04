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
use Laravel\Fortify\Contracts\LoginResponse;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('public-grafik', function (Request $request) {
            return [
                Limit::perMinute(30)->by($request->ip()),
                Limit::perMinute(45)->by($request->ip().'|'.($request->query('institution_id') ?? 'all')),
            ];
        });

        RateLimiter::for('public-publikasi', function (Request $request) {
            return [
                Limit::perMinute(8)->by($request->ip()),
                Limit::perMinute(12)->by($request->ip().'|'.($request->query('institution_id') ?? 'all')),
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
