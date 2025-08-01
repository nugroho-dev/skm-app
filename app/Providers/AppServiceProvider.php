<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        View::composer('*', function ($view) {
        $user = auth()->user();

        $menus = [];

        if ($user) {
            $role = $user->getRoleNames()->first(); // Spatie method
            $menus = MenuService::getMenu($role);
        }

        $view->with('menus', $menus);
        });
    }
}
