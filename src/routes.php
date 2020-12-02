<?php

use Illuminate\Support\Facades\Route;
use ShaitanMasters\Prometheus\Http\MetricsController;
use ShaitanMasters\Prometheus\Http\SecurityMiddleware;

Route::middleware(SecurityMiddleware::class)->group(function () {
    $route = config('prometheus_exporter.route');
    Route::get($route, MetricsController::class);
});
