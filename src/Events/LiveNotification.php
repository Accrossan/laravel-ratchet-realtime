<?php
namespace Accrossan\RatchetLaravelWebSocket\Events;

use Accrossan\RatchetLaravelWebSocket\Contracts\WebSocketEvent;

class LiveNotification extends WebSocketEvent
{
    public function __construct(
        protected int $userId,
        protected array $data
    ) {
    }

    public function channel(): string
    {
        return "user.{$this->userId}";
    }

    public function event(): string
    {
        return "notification.new";
    }

    public function payload(): array
    {
        return $this->data;
    }
}
