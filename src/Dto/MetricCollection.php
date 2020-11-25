<?php

namespace Mojam\Prometheus\Dto;

class MetricCollection
{
    private string $name;

    private string $type;

    private string $help;

    private array $labelNames;

    private array $metrics = [];

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->help = $data['help'];
        $this->labelNames = $data['labelNames'];

        foreach ($data['samples'] as $sampleData) {
            $this->metrics[] = new Metric($sampleData);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function getLabelNames(): array
    {
        return $this->labelNames;
    }

    public function hasLabelNames(): bool
    {
        return !empty($this->labelNames);
    }
}
