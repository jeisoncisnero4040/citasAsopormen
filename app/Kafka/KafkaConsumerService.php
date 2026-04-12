<?php

namespace App\Kafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use App\Exceptions\CustomExceptions\QueueException;
use App\Kafka\Ports\ConsumerQueuePort;


class KafkaConsumerService implements ConsumerQueuePort
{
    private KafkaConsumer $consumer;    
    private bool $running = true;

    private int $maxRetries = 3;
    private EventHandlerRegistry $registry;



    public function __construct(EventHandlerRegistry $registry)
    {
        $this->registry = $registry;

        $conf = new Conf();

        $conf->set('bootstrap.servers', env('KAFKA_BROKERS', 'kafka:9092'));
        $conf->set('group.id', env('KAFKA_CONSUMER_GROUP', 'default-group'));
        $conf->set('auto.offset.reset', 'earliest');
        $conf->set('enable.auto.commit', 'false');
        $conf->set('enable.partition.eof', 'true');
        $conf->set('enable.auto.offset.store', 'false');
        $conf->set('session.timeout.ms', '10000');
        $conf->set('max.poll.interval.ms', '300000');

        $conf->setRebalanceCb(function ($kafka, $err, ?array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    logger()->info('Partitions assigned', ['partitions' => $partitions]);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    logger()->warning('Partitions revoked');
                    $kafka->assign(null);
                    break;

                default:
                    throw new QueueException(
                        'Error de rebalanceo: ' . rd_kafka_err2str($err),
                        $err
                    );
            }
        });

        $this->consumer = new KafkaConsumer($conf);

        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, fn() => $this->shutdown());
        pcntl_signal(SIGINT, fn() => $this->shutdown());
    }

    public function subscribe(array $topics): void
    {
        if (empty($topics)) {
            throw new \InvalidArgumentException('Topics no pueden estar vacíos');
        }

        logger()->info('Subscribing to topics', ['topics' => $topics]);

        $this->consumer->subscribe($topics);
    }

    public function consume(): void
    {
        logger()->info('Kafka consumer started');

        while ($this->running) {

            $message = $this->consumer->consume(1000);

            switch ($message->err) {

                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $this->processMessage($message);
                    break;

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // eventos normales, no hacer nada
                    break;

                default:
                    logger()->error('Kafka error', [
                        'error' => $message->errstr(),
                        'code' => $message->err
                    ]);

                    throw new QueueException($message->errstr(), $message->err);
            }
        }

        $this->gracefulCommit();
        logger()->info('Kafka consumer stopped');
    }

    private function processMessage(Message $message): void
    {
        $retries = 0;

        while ($retries < $this->maxRetries) {

            $payload = null;

            try {
                $payload = $this->decodePayload($message);
                $event = $payload['event'];

                $handler = $this->registry->resolve($event);

                if (!$handler) {
                    throw new QueueException("No handler for event: $event");
                }

                $handler->handle($payload);

                $this->commit($message);
                return;

            } catch (\Throwable $e) {

                $retries++;

                logger()->warning('Error processing Kafka message', [
                    'event' => $payload['event'] ?? null,
                    'payload' => $payload,
                    'error' => $e->getMessage(),
                    'retry' => $retries,
                ]);

                usleep(200000 * $retries);
            }
        }

        logger()->error('Message failed after retries', [
            'payload' => $message->payload,
            'topic' => $message->topic_name,
            'partition' => $message->partition,
            'offset' => $message->offset,
        ]);

        $this->sendToDlq($message);
    }

    private function decodePayload(Message $message): array
    {
        try {
            $payload = json_decode($message->payload, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($payload['event']) || !is_string($payload['event'])) {
                throw new QueueException('Payload inválido: falta event');
            }

            return $payload;

        } catch (\Throwable $e) {
            throw new QueueException(
                'Error al decodificar payload: ' . $e->getMessage()
            );
        }
    }

    private function commit(Message $message): void
    {
        $this->consumer->commit($message);
    }

    private function gracefulCommit(): void
    {
        try {
            $this->consumer->commit();
        } catch (\Throwable $e) {
            throw new QueueException(
                'Error al hacer commit: ' . $e->getMessage()
            );
        }
    }

    private function sendToDlq(Message $message): void
    {
        logger()->error('Sending message to DLQ (simulado)', [
            'topic' => $message->topic_name,
            'partition' => $message->partition,
            'offset' => $message->offset,
            'payload' => $message->payload
        ]);

        // IMPORTANTE:
        // Aquí debería ir un KafkaProducer real hacia un topic DLQ

        $this->commit($message);
    }

    private function shutdown(): void
    {
        logger()->info('Shutdown signal received');
        $this->running = false;
    }
}