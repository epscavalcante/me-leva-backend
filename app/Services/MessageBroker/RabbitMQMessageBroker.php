<?php

namespace App\Services\MessageBroker;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageBroker implements MessageBroker
{
    private ?AMQPStreamConnection $connection = null;

    private ?AMQPChannel $channel = null;

    public function publish(string $exchange, array $data): void
    {
        $this->connect();

        $this->channel->exchange_declare(
            exchange: $exchange,
            type: AMQPExchangeType::DIRECT,
            passive: false,
            durable: true,
            auto_delete: false
        );

        $this->channel->basic_publish(
            exchange: $exchange,
            msg: new AMQPMessage(
                body: json_encode($data),
                properties: [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ]
            ),
        );

        $this->disconect();
    }

    public function consume(string $queue, Closure $callback): void
    {
        $this->connect();

        $this->channel->queue_declare(
            queue: $queue,
            durable: true,
            auto_delete: false
        );

        $this->channel->basic_consume(
            queue: $queue,
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: function (AMQPMessage $message) use ($callback) {
                $message->ack();
                $callback(json_decode($message->getBody(), true));
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

        $this->disconect();
    }

    private function connect(): void
    {
        if ($this->connection) {
            $this->channel = $this->connection->channel();

            return;
        }

        $this->connection = new AMQPStreamConnection(
            host: config('services.rabbitmq.host'),
            user: config('services.rabbitmq.user'),
            password: config('services.rabbitmq.password'),
            port: config('services.rabbitmq.port'),
            vhost: config('services.rabbitmq.vhost')
        );

        $this->channel = $this->connection->channel();
    }

    private function disconect(): void
    {
        $this->channel->close();

        $this->connection->close();
    }
}
