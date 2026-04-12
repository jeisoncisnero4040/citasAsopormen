<?php

namespace App\Kafka;

use App\Kafka\Contracts\QueueProducer;
use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\Topic;
use RuntimeException;
use App\Kafka\Domain\MessageQueue;



class KafkaProducer implements QueueProducer
{
    protected Producer $producer;

    /** @var Topic[] */
    private array $topics = [];

    private int $bufferCount = 0;
    private int $flushThreshold = 100;  

    public function __construct()
    {
        $conf = new Conf();

        $conf->set('bootstrap.servers', env('KAFKA_BROKERS', 'kafka:9092'));

        $conf->set('enable.idempotence', 'true');
        $conf->set('acks', 'all');
        $conf->set('retries', '10');
        $conf->set('max.in.flight.requests.per.connection', '5');
        $conf->set('linger.ms', '5');
        $conf->set('batch.num.messages', '1000');
        $conf->set('queue.buffering.max.messages', '100000');

        $conf->setDrMsgCb(function ($kafka, $message) {
            if ($message->err) {
                logger()->error('Kafka delivery failed', [
                    'error' => $message->errstr(),
                    'topic' => $message->topic_name,
                ]);
            }
        });
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            logger()->critical('Kafka producer error', [
                'error' => $err,
                'reason' => $reason
            ]);
        });

        $this->producer = new Producer($conf);
    }

    public function publish(MessageQueue $message): void
    {
        $topic = $this->getTopic($message->getTopic());

        $payload = json_encode($message->toArray(), JSON_THROW_ON_ERROR);

        try {
            $topic->produce(
                RD_KAFKA_PARTITION_UA,
                0,
                $payload,
                $message->getKey()
            );

            $this->bufferCount++;

        } catch (\RdKafka\Exception $e) {

            $this->handleBufferFull($message, $payload);
        }
        $this->producer->poll(0);
        if ($this->bufferCount >= $this->flushThreshold) {
            $this->flush();
        }
    }

    private function handleBufferFull(MessageQueue $message, string $payload): void
    {
        logger()->warning('Kafka buffer full, retrying...');
        $this->producer->poll(100);
        $topic = $this->getTopic($message->getTopic());
        $topic->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            $payload,
            $message->getKey(),
            $message->getHeaders()
        );
    }

    private function getTopic(string $name): Topic
    {
        if (!isset($this->topics[$name])) {
            $this->topics[$name] = $this->producer->newTopic($name);
        }

        return $this->topics[$name];
    }

    public function flush(): void
    {
        $retries = 5;

        while ($retries > 0) {
            $result = $this->producer->flush(1000);

            if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
                $this->bufferCount = 0;
                return;
            }

            $retries--;
        }

        throw new RuntimeException('Kafka flush failed');
    }

    public function __destruct()
    {

        $this->flush();
    }
}