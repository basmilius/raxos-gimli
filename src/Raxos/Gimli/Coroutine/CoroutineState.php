<?php
declare(strict_types=1);

namespace Raxos\Gimli\Coroutine;

use Raxos\Foundation\Storage\SimpleKeyValue;

/**
 * Class CoroutineState
 *
 * @mixin SimpleKeyValue
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Gimli\Coroutine
 * @since 1.0.0
 */
class CoroutineState
{

    private static ?SimpleKeyValue $store = null;

    /**
     * Returns the value with the given key from the coroutine state.
     *
     * @param string $key
     *
     * @return mixed
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public static function get(string $key): mixed
    {
        static::$store ??= new SimpleKeyValue();

        return static::$store->get($key);
    }

    /**
     * Returns TRUE if the given key exists in the coroutine state.
     *
     * @param string $key
     *
     * @return bool
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public static function has(string $key): bool
    {
        static::$store ??= new SimpleKeyValue();

        return static::$store->has($key);
    }

    /**
     * Sets a value in the coroutine state.
     *
     * @param string $key
     * @param mixed $value
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public static function set(string $key, mixed $value): void
    {
        static::$store ??= new SimpleKeyValue();
        static::$store->set($key, $value);
    }

    /**
     * Removes a value from the coroutine state.
     *
     * @param string $key
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public static function unset(string $key): void
    {
        static::$store ??= new SimpleKeyValue();
        static::$store->unset($key);
    }

}
