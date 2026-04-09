<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QueueService;
use App\Kafka\Domain\MessageQueue;
use Illuminate\Support\Str;

class KafkaController extends Controller
{
    protected QueueService $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function publish(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array',

            // opcionales pero poderosos
            'key' => 'nullable|string',
            'meta' => 'nullable|array',
            'headers' => 'nullable|array',
        ]);



        
            $message = MessageQueue::create(
                    topic: $request->input('topic'),
                    event: $request->input('event')
                )
                ->withData($request->input('data'))
                ->withMeta(array_merge([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'request_id' => Str::uuid()->toString(),
                ], $request->input('meta', [])))
                ->withHeaders(array_merge([
                    'trace_id' => $request->header('X-Trace-Id') ?? Str::uuid()->toString(),
                ], $request->input('headers', [])));

            // 🔥 key para partición (orden por entidad)
            if ($request->filled('key')) {
                $message = $message->withKey($request->input('key'));
            }

            // 🚀 enviar

            $this->queueService->publish($message);

            return response()->json([
                'status' => 'success',
                'message' => 'Evento publicado correctamente',
                'event' => $message->toArray()['event'],
                'idempotency_key' => $message->getKey(),
            ]);


    }
}