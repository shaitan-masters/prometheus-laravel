<?php

use Illuminate\Support\Facades\Route;
use Mojam\Prometheus\MetricsController;
use Mojam\Prometheus\SecurityMiddleware;

Route::middleware(SecurityMiddleware::class)->group(function () {
    Route::get('metrics', MetricsController::class);
});
