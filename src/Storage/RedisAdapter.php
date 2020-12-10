<?php

namespace ShaitanMasters\Prometheus\Storage;

use Illuminate\Redis\Connections\Connection;
use InvalidArgumentException;
use JsonException;
use ShaitanMasters\Prometheus\Collectors\Counter;
use ShaitanMasters\Prometheus\Collectors\Gauge;
use ShaitanMasters\Prometheus\Collectors\Collector;
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
            $metrics = array_merge($metrics, $this->collectGauges());
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
            'name' => $counter->getName(),
            'type' => $counter->getType(),
            'help' => $counter->getHelp(),
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
                    'name' => $counter['name'],
                    'labelNames' => [],
                    'labelValues' => json_decode($k, true, 512, JSON_THROW_ON_ERROR),
                    'value' => $value,
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

    private function toMetricKey(Collector $collector): string
    {
        return implode(':', [self::PREFIX, $collector->getType(), $collector->getName()]);
    }

    public function prepareGaugeData(Gauge $gauge, float $value, int $command): array
    {

        $metaData = [
            'name' => $gauge->getName(),
            'type' => $gauge->getType(),
            'help' => $gauge->getHelp(),
            'labelNames' => $gauge->getLabelNames(),
        ];

        return [
            'metric_key' => $this->toMetricKey($gauge),
            'general_key' => self::PREFIX . Gauge::TYPE . self::PROMETHEUS_METRIC_KEYS_SUFFIX,
            'command' => $this->getRedisCommand($command),
            'labelValues' => $gauge->getLabelsValues(),
            'value' => $value,
            'meta_data' => $metaData
        ];
    }

    public function updateGauge(array $data): void
    {

        $this->redis->eval(
            <<<LUA
local result = redis.call(ARGV[1], KEYS[1], ARGV[2], ARGV[3])
if ARGV[1] == 'hSet' then
    if result == 1 then
        redis.call('hSet', KEYS[1], '__meta', ARGV[4])
        redis.call('sAdd', KEYS[2], KEYS[1])
    end
else
    if result == ARGV[3] then
        redis.call('hSet', KEYS[1], '__meta', ARGV[4])
        redis.call('sAdd', KEYS[2], KEYS[1])
    end
end
LUA
            ,
            2,

            $data['metric_key'],
            $data['general_key'],
            $data['command'],
            json_encode($data['labelValues'], JSON_THROW_ON_ERROR),
            $data['value'],
            json_encode($data['meta_data'], JSON_THROW_ON_ERROR),

        );
    }

    private function collectGauges(): array
    {
        $generalKey = self::PREFIX . Gauge::TYPE . self::PROMETHEUS_METRIC_KEYS_SUFFIX;
        $keys = $this->redis->sMembers($generalKey);
        sort($keys);
        $gauges = [];
        foreach ($keys as $key) {

            $metricKey = str_replace($this->redis->_prefix(''), '', $key);

            $raw = $this->redis->hGetAll($metricKey);

            $this->updateGauge([
                'metric_key' => $metricKey,
                'general_key' => $generalKey,
                'command' => 'hSet',
                'labelValues' => [],
                'value' => 0,
                'meta_data' => []
            ]);

            $gauge = json_decode($raw['__meta'], true, 512, JSON_THROW_ON_ERROR);
            unset($raw['__meta']);
            $gauge['samples'] = [];
            foreach ($raw as $k => $value) {

                $gauge['samples'][] = [
                    'name' => $gauge['name'],
                    'labelNames' => [],
                    'labelValues' => json_decode($k, true, 512, JSON_THROW_ON_ERROR),
                    'value' => $value,
                ];
            }
            usort($gauge['samples'], static function ($a, $b): int {
                return strcmp(implode("", $a['labelValues']), implode("", $b['labelValues']));
            });
            $gauges[] = $gauge;
        }
        return $gauges;
    }
}
