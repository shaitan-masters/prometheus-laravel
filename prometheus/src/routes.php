<?php

use Illuminate\Support\Facades\Route;
use Mojam\Prometheus\Http\MetricsController;
use Mojam\Prometheus\Http\SecurityMiddleware;

Route::middleware(SecurityMiddleware::class)->group(function () {
    Route::get('metrics', MetricsController::class);
});
