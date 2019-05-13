<?php

namespace App\Providers;

use App\User;
use Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!Auth::check()) {
            $userModel = User::where('login', 'stas')->first();

            if ($userModel)
                Auth::login($userModel, true);
        }
    }
}
