<?php

namespace Valentin\Mojam\Dto;

class Metric
{
    private string $name;

    private array $labelNames;

    private array $labelValues;

    private string $value;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->labelNames = $data['labelNames'];
        $this->labelValues = $data['labelValues'];
        $this->value = $data['value'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabelNames(): array
    {
        return $this->labelNames;
    }

    public function getLabelValues(): array
    {
        return $this->labelValues;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function hasLabelNames(): bool
    {
        return !empty($this->labelNames);
    }
}
