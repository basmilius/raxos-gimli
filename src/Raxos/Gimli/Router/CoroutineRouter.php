<?php
declare(strict_types=1);

namespace Raxos\Gimli\Router;

use Raxos\Router\Router;

/**
 * Class CoroutineRouter
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Gimli\Router
 * @since 1.0.0
 */
class CoroutineRouter extends Router
{

    /**
     * Resolves the route mappings and call stack.
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public final function prepareForResolving(): void
    {
        if ($this->isSetupDone) {
            return;
        }

        $this->resolveMappings();
        $this->resolveCallStack();

        $this->isSetupDone = true;
    }

}
