<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\SiteController as AdminSiteController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package:slug}', [PackageController::class, 'show'])->name('packages.show');

/*
|--------------------------------------------------------------------------
| "Login to buy" from a package page needs to actually return the customer
| to that package afterward — Breeze's login controller (unmodified) calls
| redirect()->intended(...), which reads the 'url.intended' session key.
| This route populates that key from a validated same-site path before
| handing off to Breeze's own /login, so no Breeze-generated file needs
| touching. Without this, "Login to Buy" silently dumps the customer on
| the generic dashboard instead of back on the package they wanted.
|--------------------------------------------------------------------------
*/
Route::get('/login-with-redirect', function (\Illuminate\Http\Request $request) {
    $intended = $request->query('to');

    if ($intended && str_starts_with($intended, '/') && ! str_starts_with($intended, '//')) {
        $request->session()->put('url.intended', url($intended));
    }

    return redirect()->route('login');
})->name('login.with-redirect');

/*
|--------------------------------------------------------------------------
| Paystack — webhook is public (signature-verified inside the controller),
| the checkout callback needs an authenticated customer.
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])->name('webhooks.paystack');

Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/{package}', [CheckoutController::class, 'initiate'])
        ->middleware('throttle:10,1')
        ->name('checkout.initiate');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/orders/{order}', [DashboardController::class, 'showOrder'])->name('dashboard.orders.show');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Admin panel — entirely separate guard, separate login, own middleware.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:6,1');
    });

    Route::middleware('auth.admin:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('packages', AdminPackageController::class)->except('show');
        Route::resource('sites', AdminSiteController::class)->except('show');

        Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
        Route::get('/vouchers/import', [AdminVoucherController::class, 'showImportForm'])->name('vouchers.import.form');
        Route::post('/vouchers/import', [AdminVoucherController::class, 'import'])->name('vouchers.import');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [AdminOrderController::class, 'exportCsv'])->name('orders.export');
    });
});
