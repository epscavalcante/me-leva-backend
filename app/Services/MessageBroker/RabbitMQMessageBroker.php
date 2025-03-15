<?php

namespace App\Services\MessageBroker;

use Closure;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageBroker implements MessageBroker
{
    private ?AMQPStreamConnection $connection = null;

    public function __construct()
    {
        if ($this->connection) {
            return;
        }

        $this->connection = new AMQPStreamConnection(
            host: config('services.rabbitmq.host'),
            user: config('services.rabbitmq.user'),
            password: config('services.rabbitmq.password'),
            port: config('services.rabbitmq.port'),
            vhost: config('services.rabbitmq.vhost')
        );
    }

    public function publish(string $exchange, array $data): void
    {
        $channel = $this->connection->channel();
        $msg = new AMQPMessage(json_encode($data));
        $channel->basic_publish(
            msg: $msg,
            exchange: $exchange
        );
        $channel->close();
        $this->connection->close();
    }

    public function consume(string $queue, Closure $callback): void
    {
        $channel = $this->connection->channel();
        $channel->basic_consume(
            queue: $queue,
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: $callback
        );
        echo 'Waiting for new message on test_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $this->connection->close();
    }
}
