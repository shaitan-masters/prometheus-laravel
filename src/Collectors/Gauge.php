<?php


namespace ShaitanMasters\Prometheus\Collectors;

use ShaitanMasters\Prometheus\Storage\Adapter;

class Gauge extends Collector
{
    public const TYPE = 'gauge';

    public function set(float $value): void
    {
        $data = $this->storageAdapter->prepareGaugeData($this, $value, Adapter::COMMAND_SET);
        $this->storageAdapter->updateGauge($data);
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function inc(): void
    {
        $this->incBy(1);
    }

    public function incBy(float $value): void
    {
        $data = $this->storageAdapter->prepareGaugeData($this, $value, Adapter::COMMAND_INCREMENT_FLOAT);
        $this->storageAdapter->updateGauge($data);
    }


    public function dec(): void
    {
        $this->decBy(1);
    }

    public function decBy(float $value): void
    {
        $this->incBy(-$value);
    }
}
