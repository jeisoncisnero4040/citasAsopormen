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
        
        if ($request->is('api/metrics')) {
            return $next($request);
        }

        $service = 'clinico'; 
        $method = $request->method();
        $route = $request->route();
        $endpoint = $route ? $route->uri() : $request->path();

        
        $endpoint = preg_replace('/\d+/', '{var}', $endpoint);
        $endpoint = preg_replace('/[a-f0-9]{6,}/i', '{var}', $endpoint);

        $start = microtime(true);

        try {
            $response = $next($request);
            $status = $response->status();
        } catch (\Throwable $e) {
            $status = 500;
            throw $e;
        }

        $duration = microtime(true) - $start;

        
        $counter = $this->registry->getOrRegisterCounter(
            'http_requests', 
            'total',
            'Total HTTP requests',
            ['service', 'method', 'endpoint', 'status']
        );
        $counter->incBy(1, [
            (string)$service,
            (string)$method,
            (string)$endpoint,
            (string)$status
        ]);

        
        $histogram = $this->registry->getOrRegisterHistogram(
            'http_requests',
            'latency_seconds',
            'Request latency in seconds',
            ['service', 'method', 'endpoint'],
            [0.01, 0.05, 0.1, 0.3, 0.5, 1, 2, 5, 10] 
        );
        $histogram->observe($duration, [
            (string)$service,
            (string)$method,
            (string)$endpoint
        ]);

        return $response;
    }
}
