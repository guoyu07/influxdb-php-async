<?php
/**
 * @license See the file LICENSE for copying permission
 */

declare(strict_types = 1);

namespace Thorr\InfluxDBAsync;

use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;

abstract class AbstractClient implements AsyncClient
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(array $options = [], LoopInterface $loop = null)
    {
        $this->options = array_merge(static::DEFAULT_OPTIONS, $options);

        if (! $loop) {
            $loop = LoopFactory::create();
        }

        $this->loop = $loop;
    }

    public function selectDatabase(string $database): void
    {
        $this->options['database'] = $database;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function run(): void
    {
        $this->loop->run();
    }

    protected function createBaseUri(): string
    {
        $options = $this->getOptions();
        $scheme  = 'http' . ($options['ssl'] ? 's' : '');

        return sprintf('%s://%s:%d', $scheme, $options['host'], $options['port']);
    }

    protected function createQueryUrl(string $query, array $params): string
    {
        $params['db'] = $params['db'] ?? $this->getOptions()['database'];
        $params['q']  = $query;

        return 'query?' . http_build_query($params);
    }
}