<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\QrCodeRepositoryInterface;
use App\Repositories\QrCodeRepository;
use App\Repositories\FormRepositoryInterface;
use App\Repositories\FormRepository;
use App\Repositories\FormResponseRepositoryInterface;
use App\Repositories\FormResponseRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind QR Code Repository
        $this->app->bind(QrCodeRepositoryInterface::class, QrCodeRepository::class);
        
        // Bind Form Repositories
        $this->app->bind(FormRepositoryInterface::class, FormRepository::class);
        $this->app->bind(FormResponseRepositoryInterface::class, FormResponseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
