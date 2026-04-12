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
        // Evita medir el propio endpoint de métricas
        if ($request->is('api/metrics')) {
            return $next($request);
        }

        $service = 'crm'; 
        $method = $request->method();
        $route = $request->route();
        $endpoint = $route ? $route->uri() : $request->path();

        // Reemplazo de números o hashes para reducir series (opcional, puedes comentar si no quieres)
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

        // Contador de peticiones (misma métrica que ya tienes)
        $counter = $this->registry->getOrRegisterCounter(
            'http_requests', // mismo nombre
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

        // Histograma de latencia (misma métrica que ya tienes)
        $histogram = $this->registry->getOrRegisterHistogram(
            'http_requests', // mismo nombre
            'latency_seconds',
            'Request latency in seconds',
            ['service', 'method', 'endpoint'],
            [0.01, 0.05, 0.1, 0.3, 0.5, 1, 2, 5, 10] // buckets predefinidos
        );
        $histogram->observe($duration, [
            (string)$service,
            (string)$method,
            (string)$endpoint
        ]);

        return $response;
    }
}
