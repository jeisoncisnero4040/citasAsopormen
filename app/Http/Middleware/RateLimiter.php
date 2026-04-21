<?php

namespace App\Http\Middleware;

use App\Services\CacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\CustomExceptions\RateLimitException;

class RateLimiter
{
    private const TTL = 60;
    private const MAX_REQUESTS = 60;

    public function __construct(
        private CacheService $cache
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local')) {
            error_log('RateLimiter skipped in local environment');
            return $next($request);
        }

        $key = $this->resolveKey($request);
        $count = $this->cache->increment($key, self::TTL);
        if ((int) $count > self::MAX_REQUESTS) {
            throw new RateLimitException(
                "Muchas solicitudes en poco tiempo",
                429
            );
        }

        return $next($request);
    }
    private function resolveKey(Request $request): string
    {
        if ($token = $request->bearerToken()) {
            return 'rate:token:' . hash('sha256', $token);
        }

        return 'rate:ip:' . $request->ip();
    }

}
