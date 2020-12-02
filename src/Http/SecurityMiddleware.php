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
        $shouldBeUsed = config('prometheus.use_security_middleware', false);

        if (!$shouldBeUsed) {
            return $next($request);
        }

        $bearerToken = $request->bearerToken();

        if ($bearerToken === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $apiToken = config('prometheus.api_token', '');

        if ($bearerToken !== $apiToken) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
