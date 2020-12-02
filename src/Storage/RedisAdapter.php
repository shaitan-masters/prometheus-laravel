<?php

namespace ShaitanMasters\Prometheus\Storage;

use Illuminate\Redis\Connections\Connection;
use InvalidArgumentException;
use JsonException;
use ShaitanMasters\Prometheus\Collectors\Counter;
use ShaitanMasters\Prometheus\Dto\MetricCollection;

class RedisAdapter implements Adapter
{
    private const PROMETHEUS_METRIC_KEYS_SUFFIX = '_METRIC_KEYS';
    private const PREFIX = 'PROMETHEUS_';
    private Connection $redis;

    public function __construct(Connection $redis)
    {
        $this->redis = $redis;
    }

    public function collect(): array
    {
        try {
            $metrics = $this->collectCounters();
        } catch (JsonException $e) {
            $metrics = [];
        }

        return array_map(
            static function (array $metric) {
                return new MetricCollection($metric);
            },
            $metrics
        );
    }

    public function updateCounter(Counter $counter, int $value, int $command): void
    {
        $metaData = [
            'name'       => $counter->getName(),
            'type'       => $counter->getType(),
            'help'       => $counter->getHelp(),
            'labelNames' => $counter->getLabelNames(),
        ];

        $this->redis->eval(
            <<<LUA
                local result = redis.call(ARGV[1], KEYS[1], ARGV[3], ARGV[2])
                if result == tonumber(ARGV[2]) then
                    redis.call('hMSet', KEYS[1], '__meta', ARGV[4])
                    redis.call('sAdd', KEYS[2], KEYS[1])
                end
                return result
            LUA
            ,
            2,
            $this->toMetricKey($counter),
            self::PREFIX . Counter::TYPE . self::PROMETHEUS_METRIC_KEYS_SUFFIX,
            $this->getRedisCommand($command),
            $value,
            json_encode($counter->getLabelsValues(), JSON_THROW_ON_ERROR),
            json_encode($metaData, JSON_THROW_ON_ERROR)
        );
    }

    private function collectCounters(): array
    {
        $keys = $this->redis->smembers(self::PREFIX . Counter::TYPE . self::PROMETHEUS_METRIC_KEYS_SUFFIX);
        sort($keys);
        $counters = [];

        foreach ($keys as $key) {
            $raw = $this->redis->hgetall(str_replace($this->redis->_prefix(''), '', $key));
            $counter = json_decode($raw['__meta'], true, 512, JSON_THROW_ON_ERROR);
            unset($raw['__meta']);
            $counter['samples'] = [];

            foreach ($raw as $k => $value) {
                $counter['samples'][] = [
                    'name'        => $counter['name'],
                    'labelNames'  => [],
                    'labelValues' => json_decode($k, true, 512, JSON_THROW_ON_ERROR),
                    'value'       => $value,
                ];
            }
            usort($counter['samples'], static function ($a, $b) {
                return strcmp(implode('', $a['labelValues']), implode('', $b['labelValues']));
            });
            $counters[] = $counter;
        }

        return $counters;
    }

    private function getRedisCommand(int $command): string
    {
        switch ($command) {
            case Adapter::COMMAND_INCREMENT_INTEGER:
                return 'hIncrBy';
            case Adapter::COMMAND_INCREMENT_FLOAT:
                return 'hIncrByFloat';
            case Adapter::COMMAND_SET:
                return 'hSet';
            default:
                throw new InvalidArgumentException('Unknown command');
        }
    }

    private function toMetricKey(Counter $counter): string
    {
        return implode(':', [self::PREFIX, $counter->getType(), $counter->getName()]);
    }
}
