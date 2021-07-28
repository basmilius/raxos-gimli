<?php
declare(strict_types=1);

namespace Raxos\Gimli\Http;

use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPv4;
use Raxos\Foundation\Network\IPv6;
use Raxos\Foundation\Storage\ReadonlyKeyValue;
use Raxos\Foundation\Storage\SimpleKeyValue;
use Raxos\Foundation\Util\ArrayUtil;
use Raxos\Http\HttpFile;
use Raxos\Http\HttpRequest;
use Raxos\Http\UserAgent;
use Swoole\Http\Request as SwooleRequest;

/**
 * Class Request
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Gimli\Http
 * @since
 */
class Request extends HttpRequest
{

    protected string $body;

    /**
     * Request constructor.
     *
     * @param SwooleRequest $swooleRequest
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function __construct(SwooleRequest $swooleRequest)
    {
        if (false === true) {
            parent::__construct();
        }

        $this->cache = new SimpleKeyValue();
        $this->cookies = new ReadonlyKeyValue($swooleRequest->cookie);
        $this->files = self::convertSwooleFiles($swooleRequest->files ?? []);
        $this->headers = new ReadonlyKeyValue($swooleRequest->header);
        $this->post = new ReadonlyKeyValue($swooleRequest->post ?? []);
        $this->queryString = new ReadonlyKeyValue($swooleRequest->get ?? []);
        $this->server = new ReadonlyKeyValue($swooleRequest->server);

        $this->body = $swooleRequest->getContent();
        $this->method = \strtolower($swooleRequest->getMethod());
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function pathName(): string
    {
        return $this->server->get('request_uri');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function uri(): string
    {
        $queryString = $this->server->get('query_string');

        if ($queryString !== null) {
            return $this->server->get('request_uri') . '?' . $queryString;
        }

        return $this->server->get('request_uri');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function ip(): IPv4|IPv6|null
    {
        if ($this->cache->has('ip')) {
            return $this->cache->get('ip');
        }

        $ip = IP::parse($this->server->get('remote_addr'));

        $this->cache->set('ip', $ip);

        return $ip;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function isSecure(): bool
    {
        // todo(Bas): need a better solution for this.
        return (int)$this->server->get('server_port') === 443;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function bodyString(): string
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    public function userAgent(): UserAgent
    {
        if ($this->cache->has('user_agent')) {
            return $this->cache->get('user_agent');
        }

        $ua = new UserAgent($this->headers->get('user-agent', 'Raxos/1.0'));

        $this->cache->set('user_agent', $ua);

        return $ua;
    }

    /**
     * Converts the incoming swoole request files to our key value store.
     *
     * @param array $swooleFiles
     *
     * @return ReadonlyKeyValue
     * @author Bas Milius <bas@glybe.nl>
     * @since 1.0.0
     */
    private static function convertSwooleFiles(array $swooleFiles): ReadonlyKeyValue
    {
        $files = [];

        foreach ($swooleFiles as $name => $value) {
            if (ArrayUtil::isSequential($value)) {
                $files[$name] ??= [];

                foreach ($value as $file) {
                    $files[$name][] = new HttpFile($file);
                }
            } else {
                $files[$name] = new HttpFile($value);
            }
        }

        return new ReadonlyKeyValue($files);
    }

}
