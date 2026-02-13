<?php
namespace Accrossan\RatchetLaravelWebSocket\WebSocket;

use Predis\Client;


class RedisSubscriber
{
    public function __construct(protected WsServer $ws, protected ?Client $redis = null)
    {
        $this->redis = $this->redis ?: new Client(config('database.redis.default') + [
            'read_write_timeout' => -1,
        ]);
    }



    public function listen()
    {
        $this->redis->pubSubLoop(['subscribe' => 'realtime'], function ($pubsub, $msg) {
            if ($msg->kind === 'message') {
                echo "Redis message received: {$msg->payload}\n";
                $data = json_decode($msg->payload, true);
                if (isset($data['channel'])) {
                    $this->ws->broadcast($data['channel'], $msg->payload);
                }
            }
        });

    }

}
