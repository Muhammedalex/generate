<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\QrCodeRepositoryInterface;
use App\Repositories\QrCodeRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind QR Code Repository
        $this->app->bind(QrCodeRepositoryInterface::class, QrCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
