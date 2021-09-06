<?php
declare(strict_types=1);

namespace Raxos\Gimli\Http;

use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http\Server;

/**
 * Class HttpServer
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Gimli\Http
 * @since 1.0.0
 */
class HttpServer
{

    protected Server $server;

    /**
     * HttpServer constructor.
     *
     * @param string $host
     * @param int $port
     * @param int $mode
     * @param int $socketType
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(string $host, int $port, int $mode = SWOOLE_PROCESS, int $socketType = SWOOLE_SOCK_TCP)
    {
        $this->server = new Server($host, $port, $mode, $socketType);
    }

    /**
     * Starts the server.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function start(): bool
    {
        $this->server->on('request', fn(SwooleRequest $request, SwooleResponse $response) => $this->handleOnRequest($request, $response));
        $this->server->on('shutdown', fn() => $this->onShutdown());
        $this->server->on('start', fn() => $this->onStart());
        $this->server->on('workerExit', fn(Server $server, int $workerId) => $this->onWorkerExit($workerId));
        $this->server->on('workerStart', fn(Server $server, int $workerId) => $this->onWorkerStart($workerId));
        $this->server->on('workerStop', fn(Server $server, int $workerId) => $this->onWorkerStop($workerId));

        return @$this->server->start();
    }

    /**
     * Applies the given options to the server.
     *
     * @param array $options
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected final function options(array $options): static
    {
        $this->server->set($options);

        return $this;
    }

    /**
     * Invoked on an incoming request.
     *
     * @param Request $request
     * @param SwooleResponse $response
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onRequest(Request $request, SwooleResponse $response): void
    {
    }

    /**
     * Invoked when the server is shutting down.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onShutdown(): void
    {
        echo "Http server at port {$this->server->port} is shutting down..", PHP_EOL;
    }

    /**
     * Invoked when the server has started.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onStart(): void
    {
        echo "Http server listening on port {$this->server->port}.", PHP_EOL;
    }

    /**
     * Invoked when a worker has exited.
     *
     * @param int $workerId
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onWorkerExit(int $workerId): void
    {
    }

    /**
     * Invoked when a worker has started.
     *
     * @param int $workerId
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onWorkerStart(int $workerId): void
    {
    }

    /**
     * Invoked when a worker stops.
     *
     * @param int $workerId
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onWorkerStop(int $workerId): void
    {
    }

    /**
     * Handles the request event.
     *
     * @param SwooleRequest $request
     * @param SwooleResponse $response
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private function handleOnRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        $this->onRequest(new Request($request), $response);
    }

}
