<?php

namespace App\Providers;

use App\Models\Antrian;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        try {
            // Cek apakah tabel exists
            if (Schema::hasTable('antrians')) {
                $antrians = Antrian::all();
                View::share('antrians', $antrians);
            }
        } catch (\Exception $e) {
            // Abaikan error jika tabel belum ada
        }
    }
}
