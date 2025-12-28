<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tagihan;
use App\Models\Pengaduan;

use App\Observers\TagihanObserver;
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

public function boot()
{
    view()->composer('*', function ($view) {
        // Ambil pengaduan yang statusnya "baru"
        $jumlahPengaduanBaru = Pengaduan::where('status', 'baru')->count();

        $view->with('jumlahPengaduanBaru', $jumlahPengaduanBaru);
    });
}

}
