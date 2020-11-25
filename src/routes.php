<?php

use Illuminate\Support\Facades\Route;
use Valentin\Mojam\Http\MetricsController;
use Valentin\Mojam\Http\SecurityMiddleware;

Route::middleware(SecurityMiddleware::class)->group(function () {
    Route::get('metrics', MetricsController::class);
});
