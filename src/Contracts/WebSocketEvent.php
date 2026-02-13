<?php
namespace Accrossan\RatchetLaravelWebSocket\Contracts;

abstract class WebSocketEvent
{
    abstract public function channel(): string;
    abstract public function event(): string;
    abstract public function payload(): array;

    public function toMessage(): string
    {
        return json_encode([
            'event' => $this->event(),
            'channel' => $this->channel(),
            'payload' => $this->payload(),
        ]);
    }
}
