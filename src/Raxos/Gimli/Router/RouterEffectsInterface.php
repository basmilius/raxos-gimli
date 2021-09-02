<?php
declare(strict_types=1);

namespace Raxos\Gimli\Router;

use Raxos\Gimli\Http\Request;
use Raxos\Router\Effect\NotFoundEffect;
use Raxos\Router\Effect\RedirectEffect;
use Raxos\Router\Effect\ResponseEffect;
use Raxos\Router\Effect\VoidEffect;
use Raxos\Router\Error\RouterException;
use Swoole\Http\Response as SwooleResponse;

/**
 * Interface RouterEffectsInterface
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Router
 * @since 1.0.0
 */
interface RouterEffectsInterface
{

    /**
     * Invoked on a not found effect.
     *
     * @param Request $request
     * @param SwooleResponse $response
     * @param NotFoundEffect $effect
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onNotFoundEffect(Request $request, SwooleResponse $response, NotFoundEffect $effect): void;

    /**
     * Invoked on a redirect effect.
     *
     * @param Request $request
     * @param SwooleResponse $response
     * @param RedirectEffect $effect
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onRedirectEffect(Request $request, SwooleResponse $response, RedirectEffect $effect): void;

    /**
     * Invoked on a response effect.
     *
     * @param Request $request
     * @param SwooleResponse $response
     * @param ResponseEffect $effect
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onResponseEffect(Request $request, SwooleResponse $response, ResponseEffect $effect): void;

    /**
     * Invoked on a void effect.
     *
     * @param Request $request
     * @param SwooleResponse $response
     * @param VoidEffect $effect
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onVoidEffect(Request $request, SwooleResponse $response, VoidEffect $effect): void;

    /**
     * Invoked on a router exception.
     *
     * @param Request $request
     * @param SwooleResponse $response
     * @param RouterException $exception
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function onRouterException(Request $request, SwooleResponse $response, RouterException $exception): void;

}
