<?php
namespace Accrossan\RatchetLaravelWebSocket\Listeners;


use Accrossan\RatchetLaravelWebSocket\Contracts\WebSocketEvent;
use Illuminate\Support\Facades\Redis;


class PublishToRealtime
{
    public function handle(WebSocketEvent $event)
    {
        Redis::publish('realtime', $event->toMessage());
    }
}