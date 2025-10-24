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
        // Evita medir el propio endpoint de m  tricas
        if ($request->is('api/metrics')) {
            return $next($request);
        }

        $service = 'clinico'; 
        $method = $request->method();
        $route = $request->route();

        // Normaliza el endpoint (quita par  metros din  micos)
        $endpoint = $route ? $route->uri() : $request->path();
        $endpoint = preg_replace('/\{[^}]+\}/', '{var}', $endpoint);

        $start = microtime(true);

        try {
            $response = $next($request);
            $status = $response->status();
        } catch (\Throwable $e) {
            $status = 500;
            throw $e;
        }

        $duration = microtime(true) - $start;
        // Contador de peticiones
        $counter = $this->registry->getOrRegisterCounter(
            'http_requests',
            'total',
            'Total HTTP requests',
            ['service', 'method', 'endpoint', 'status']
        );
        $counter->incBy(1, [$service, $method, $endpoint, $status]);

        // Histograma de latencia
        $histogram = $this->registry->getOrRegisterHistogram(
            'http_requests',
            'latency_seconds',
            'Request latency in seconds',
            ['service', 'method', 'endpoint']
        );
        $histogram->observe($duration, [$service, $method, $endpoint]);

        return $response;
    }
}
