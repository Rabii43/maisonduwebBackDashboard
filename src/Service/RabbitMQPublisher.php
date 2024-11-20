<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    private $connection;
    private $channel;

    public function __construct(string $host, int $port, string $user, string $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function publish(string $exchange, string $routingKey, string $message): void
    {
        $this->channel->exchange_declare($exchange, 'direct', false, true, false);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
