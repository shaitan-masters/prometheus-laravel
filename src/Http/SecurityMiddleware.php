<?php

namespace Mojam\Prometheus\Http;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param mixed|null               $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $secret = $request->get('secret');

        if (!$secret) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $apiKey = config('prometheus_exporter.apiKey');

        if ($secret !== $apiKey) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
