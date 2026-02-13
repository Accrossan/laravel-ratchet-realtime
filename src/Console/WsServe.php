<?php

namespace Accrossan\RatchetLaravelWebSocket\Console;

use Accrossan\RatchetLaravelWebSocket\WebSocket\RedisSubscriber;
use Accrossan\RatchetLaravelWebSocket\WebSocket\WsServer;
use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer as RatchetWs;

class WsServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ws:serve {--redis : Only run the Redis subscriber}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Ratchet WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = config('realtime.port', 8080);
        $ws = app(WsServer::class);

        if ($this->option('redis')) {
            $this->info("Starting Redis subscriber only...");
            app(RedisSubscriber::class)->listen();

            return;
        }

        $this->info("Starting WebSocket server on port {$port}...");


        // Redis listener (async)
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            if (pcntl_fork() === 0) {
                $this->info("Starting Redis subscriber...");
                app(RedisSubscriber::class)->listen();
                exit;
            }

        } else {
            $this->warn("PCNTL extension not loaded. Redis subscriber will not run in a separate process.");
            $this->info("You might want to run the Redis subscriber separately.");
        }

        $server = IoServer::factory(
            new HttpServer(new RatchetWs($ws)),
            $port
        );

        $this->info("Server running!");
        $server->run();
    }
}

