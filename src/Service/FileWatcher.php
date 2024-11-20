<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FileWatcher
{
    private string $filePath;
    private string $lastModified;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->lastModified = filemtime($filePath);
    }

    public function watch()
    {
        while (true) {
            clearstatcache(true, $this->filePath);
            $currentModified = filemtime($this->filePath);

            if ($currentModified !== $this->lastModified) {
                $this->lastModified = $currentModified;
                $this->sendCurrencyData();
            }
// Poll every second
            sleep(1);
        }
    }

    private function sendCurrencyData()
    {
        $data = file_get_contents($this->filePath);
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('currency_updates', false, false, false, false);
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, '', 'currency_updates');

        $channel->close();
        $connection->close();
    }
}
