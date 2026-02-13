<?php
namespace Accrossan\RatchetLaravelWebSocket\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WsServer implements MessageComponentInterface
{
    protected $clients;
    protected array $subscriptions = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! (" . spl_object_id($conn) . ")\n";
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg, true);
        echo "Message received from " . spl_object_id($conn) . ": {$msg}\n";

        if (($data['action'] ?? null) === 'subscribe') {
            $this->subscriptions[spl_object_id($conn)][] = $data['channel'];
            echo "Connection " . spl_object_id($conn) . " subscribed to channel: {$data['channel']}\n";
        }
    }

    public function broadcast(string $channel, string $message)
    {
        echo "Broadcasting to channel {$channel}: {$message}\n";
        foreach ($this->clients as $client) {
            if (in_array($channel, $this->subscriptions[spl_object_id($client)] ?? [])) {
                $client->send($message);
                echo "Sent to client " . spl_object_id($client) . "\n";
            }
        }
    }



    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->subscriptions[spl_object_id($conn)]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
