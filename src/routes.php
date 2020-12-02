<?php

use Illuminate\Support\Facades\Route;
use ShaitanMasters\Prometheus\Http\MetricsController;
use ShaitanMasters\Prometheus\Http\SecurityMiddleware;

Route::middleware(SecurityMiddleware::class)->group(function () {
    $uri = config('prometheus.route_uri');
    Route::get($uri, MetricsController::class);
});
