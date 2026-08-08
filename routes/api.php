<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes — placeholder for the future mobile app (per spec's
| "Optional API" / "Future Ready" sections).
|--------------------------------------------------------------------------
| Not built out yet. When needed: install Sanctum, add
| Route::middleware('auth:sanctum') groups mirroring the web
| PackageController / CheckoutController / DashboardController,
| returning JSON via API Resources instead of Blade views.
*/

Route::get('/packages', function () {
    return \App\Models\Package::active()->get(['id', 'name', 'slug', 'price', 'duration_label', 'device_limit']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
