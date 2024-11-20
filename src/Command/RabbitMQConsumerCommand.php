<?php

namespace App\Command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RabbitMQConsumerCommand extends Command
{
    protected static $defaultName = 'app:consume-rabbitmq';

    protected function configure(): void
    {
        $this
            ->setDescription('Consumes messages from RabbitMQ.')
            ->setHelp('This command listens to a RabbitMQ queue and processes incoming messages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Connect to RabbitMQ
        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(RABBITMQ_HOST, 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // Declare the queue
        $channel->queue_declare('currency_updates', false, false, false, false);

        // Callback function to process messages
        $callback = function ($msg) use ($output) {
            $output->writeln('Received: ' . $msg->body);
            // Process the message here
        };

        // Consume messages
        $channel->basic_consume('currency_updates', '', false, true, false, false, $callback);

        // Wait for incoming messages in a loop
        while ($channel->is_consuming() || $channel->callbacks) {
            $channel->wait(); // Wait for a message
        }

        // Close connections
        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}
