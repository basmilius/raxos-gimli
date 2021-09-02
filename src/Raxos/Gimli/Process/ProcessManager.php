<?php
declare(strict_types=1);

namespace Raxos\Gimli\Process;

use Swoole\Process\Manager;

/**
 * Class ProcessManager
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Gimli\Process
 * @since 1.0.0
 */
class ProcessManager
{

    private Manager $manager;

    /**
     * ProcessManager constructor.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->manager = new Manager();
    }

    /**
     * Adds the given process to the process manager.
     *
     * @param callable $fn
     * @param bool $enableCoroutine
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function add(callable $fn, bool $enableCoroutine = false): static
    {
        $this->manager->add($fn, $enableCoroutine);

        return $this;
    }

    /**
     * Runs all processes that are registered with the process manager.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function start(): void
    {
        $this->manager->start();
    }

    /**
     * Returns a new instance of the process manager.
     *
     * @return static
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public static function new(): static
    {
        return new static;
    }

}
