<?php

namespace ShaitanMasters\Prometheus\Http;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed|null $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        $useMiddleware = config('use_default_security_middleware');

        if (!$useMiddleware) {

            return $next($request);
        }

        $token = $request->bearerToken();

        if ($token === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $apiKey = config('prometheus_exporter.api_token');

        if ($token !== $apiKey) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
