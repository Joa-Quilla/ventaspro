<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Observers\PaymentObserver;
use App\Observers\PurchaseItemObserver;
use App\Observers\PurchaseObserver;
use App\Observers\SaleObserver;
use Illuminate\Support\ServiceProvider;

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
        // Registrar Observers para manejar stock y créditos automáticamente
        Sale::observe(SaleObserver::class);
        Purchase::observe(PurchaseObserver::class);
        PurchaseItem::observe(PurchaseItemObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
