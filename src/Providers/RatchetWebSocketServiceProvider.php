<?php

namespace Accrossan\RatchetLaravelWebSocket\Providers;

use Accrossan\RatchetLaravelWebSocket\Console\WsServe;
use Accrossan\RatchetLaravelWebSocket\WebSocket\RedisSubscriber;
use Accrossan\RatchetLaravelWebSocket\WebSocket\WsServer;

use Illuminate\Support\ServiceProvider;

class RatchetWebSocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WsServer::class, function () {
            return new WsServer();
        });

        $this->app->singleton(RedisSubscriber::class, function ($app) {
            return new RedisSubscriber($app->make(WsServer::class));
        });
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \Accrossan\RatchetLaravelWebSocket\Contracts\WebSocketEvent::class,
            \Accrossan\RatchetLaravelWebSocket\Listeners\PublishToRealtime::class
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                WsServe::class,
            ]);
        }
    }
}

