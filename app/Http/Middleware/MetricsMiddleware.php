<?php

namespace App\Http\Middleware;

use Closure;
use Prometheus\CollectorRegistry;

class MetricsMiddleware
{
    private CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle($request, Closure $next)
    {
        $counter = $this->registry->getOrRegisterCounter(
            'http_requests',
            'total',
            'Total HTTP requests',
            ['method', 'endpoint', 'status']
        );

        $start = microtime(true);

        try {
            $response = $next($request);
            $status = $response->status();
        } catch (\Throwable $e) {
            $status = 500; 
            throw $e;       
        }

        $duration = microtime(true) - $start;
        $counter->incBy(1, [$request->method(), $request->path(), $status]);

        $histogram = $this->registry->getOrRegisterHistogram(
            'http_requests',
            'latency_seconds',
            'Request latency in seconds',
            ['method', 'endpoint']
        );
        $histogram->observe($duration, [$request->method(), $request->path()]);

        return $response;
    }
}
