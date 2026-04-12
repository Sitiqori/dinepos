<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Inject notif count ke semua view yang pakai sidebar
        View::composer('layouts.partials.sidebar', function ($view) {
            try {
                $hasMinStock   = Schema::hasColumn('products', 'min_stock');
                $hasExpiryDate = Schema::hasColumn('products', 'expiry_date');

                $stok = $hasMinStock
                    ? Product::whereColumn('stock', '<=', 'min_stock')->count()
                    : Product::where('stock', '<=', 5)->count();

                $expiring = 0;
                $expired  = 0;
                if ($hasExpiryDate) {
                    $today    = Carbon::today();
                    $expiring = Product::whereNotNull('expiry_date')
                        ->whereDate('expiry_date', '>=', $today)
                        ->whereDate('expiry_date', '<=', Carbon::today()->addDays(30))
                        ->count();
                    $expired  = Product::whereNotNull('expiry_date')
                        ->whereDate('expiry_date', '<', $today)
                        ->count();
                }

                $pending = Order::where('status', 'pending')->count();

                $view->with('sidebarNotifCount', $stok + $expiring + $expired + $pending);
            } catch (\Throwable $e) {
                $view->with('sidebarNotifCount', 0);
            }
        });
    }
}