<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Kafka\EventHandlerRegistry;
use App\Kafka\KafkaConsumerService;
use App\Kafka\Config\TopicsConfig;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume Kafka topics';

    public function handle()
    {

        $handlers = [
            app(\App\Kafka\Strategies\ChatUpdatedHandler::class)

        ];
        $registry = new EventHandlerRegistry($handlers);
        $consumer = new KafkaConsumerService($registry);
        $consumer->subscribe(TopicsConfig::ALLOWED_TOPICS);

        $consumer->consume();

    }
}