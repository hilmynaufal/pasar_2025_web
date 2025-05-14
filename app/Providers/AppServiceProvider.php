<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Carbon\Carbon;

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
        // Livewire::setLayout('layouts.app.blade.php'); // Sesuaikan dengan layout baru kamu
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
    }
}
