<?php
declare(strict_types=1);

namespace Raxos\Gimli\Http;

use Raxos\Gimli\Coroutine\CoroutineState;
use Raxos\Gimli\Router\CoroutineRouter;
use Raxos\Gimli\Router\RouterEffectsInterface;
use Raxos\Http\HttpCode;
use Raxos\Router\Effect\NotFoundEffect;
use Raxos\Router\Effect\RedirectEffect;
use Raxos\Router\Effect\ResponseEffect;
use Raxos\Router\Effect\VoidEffect;
use Raxos\Router\Error\RouterException;
use Raxos\Router\Error\RuntimeException;
use Swoole\Http\Response as SwooleResponse;
use function json_encode;
use function sprintf;

/**
 * Class RouterHttpServer
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Gimli\Http
 * @since 1.0.0
 */
abstract class RouterHttpServer extends HttpServer implements RouterEffectsInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onNotFoundEffect(Request $request, SwooleResponse $response, NotFoundEffect $effect): void
    {
        $response->status(HttpCode::NOT_FOUND);
        $response->end('Endpoint not found.');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onRedirectEffect(Request $request, SwooleResponse $response, RedirectEffect $effect): void
    {
        $response->status($effect->getResponseCode());
        $response->header('Location', $effect->getDestination());
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onResponseEffect(Request $request, SwooleResponse $response, ResponseEffect $effect): void
    {
        $res = $effect->getResponse();
        $res->prepareHeaders();

        $response->status($res->getResponseCode());

        foreach ($res->getHeaders() as $name => $value) {
            $response->header($name, $value);
        }

        $response->end($res->prepareBody());
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onVoidEffect(Request $request, SwooleResponse $response, VoidEffect $effect): void
    {
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onRouterException(Request $request, SwooleResponse $response, RouterException $exception): void
    {
        $response->status(500);
        $response->end(json_encode($exception));
    }

    /**
     * Registers the controllers available in the router.
     *
     * @param CoroutineRouter $router
     *
     * @throws RouterException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected abstract function registerControllers(CoroutineRouter $router): void;

    /**
     * Registers the globals that should be available in the router.
     *
     * @param CoroutineRouter $router
     *
     * @throws RouterException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected abstract function registerGlobals(CoroutineRouter $router): void;

    /**
     * Registers the globals that should be available on a per-request basis.
     *
     * @param CoroutineRouter $router
     * @param Request $request
     *
     * @throws RouterException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected abstract function registerRequestGlobals(CoroutineRouter $router, Request $request): void;

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onRequest(Request $request, SwooleResponse $response): void
    {
        parent::onRequest($request, $response);

        /** @var CoroutineRouter $router */
        $router = CoroutineState::get('router');

        try {
            $this->registerRequestGlobals($router, $request);

            $effect = $router->resolve($request->method(), $request->pathName());

            switch (true) {
                case $effect instanceof NotFoundEffect:
                    $this->onNotFoundEffect($request, $response, $effect);
                    break;

                case $effect instanceof RedirectEffect:
                    $this->onRedirectEffect($request, $response, $effect);
                    break;

                case $effect instanceof ResponseEffect:
                    $this->onResponseEffect($request, $response, $effect);
                    break;

                case $effect instanceof VoidEffect:
                    $this->onVoidEffect($request, $response, $effect);
                    break;

                default:
                    throw new RuntimeException(sprintf('Could not handle effect with class "%s".', $effect::class), RuntimeException::ERR_INSTANCE_NOT_FOUND);
            }
        } catch (RouterException $err) {
            $this->onRouterException($request, $response, $err);
        }
    }

    /**
     * Invoked when the router is available to a worker.
     *
     * @param CoroutineRouter $router
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onRouterAvailable(CoroutineRouter $router): void
    {
    }

    /**
     * {@inheritdoc}
     * @throws RouterException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    protected function onWorkerStart(int $workerId): void
    {
        parent::onWorkerStart($workerId);

        $router = new CoroutineRouter();

        $this->registerControllers($router);
        $this->registerGlobals($router);
        $this->onRouterAvailable($router);

        $router->prepareForResolving();

        CoroutineState::set('router', $router);
    }

}
