<?php

namespace ShaitanMasters\Prometheus\Collectors;

use InvalidArgumentException;
use Illuminate\Support\Str;
use ShaitanMasters\Prometheus\Storage\Adapter;
use ShaitanMasters\Prometheus\Storage\StorageFactory;

abstract class Collector
{
    public const RE_METRIC_LABEL_NAME = '/^[a-zA-Z_:][a-zA-Z0-9_:]*$/';

    protected Adapter $storageAdapter;

    protected string $namespace;

    protected string $name;

    protected string $help;

    protected array $labels = [];

    protected array $labelsValues = [];

    public function __construct()
    {
        $storageFactory = new StorageFactory();
        $this->storageAdapter = $storageFactory->getAdapter();
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function setName(string $name): void
    {
        $this->namespace = $this->namespace ? $this->namespace . '_' : '';
        $metricName = Str::snake($this->namespace . $name);

        if (!preg_match(self::RE_METRIC_LABEL_NAME, $metricName)) {
            throw new InvalidArgumentException("Invalid metric name: '" . $metricName . "'");
        }
        $this->name = $metricName;
    }

    public function setHelp(string $help): void
    {
        $this->help = $help;
    }

    public function setLabels(array $labels): void
    {
        foreach ($labels as $label) {
            if (!preg_match(self::RE_METRIC_LABEL_NAME, $label)) {
                throw new InvalidArgumentException("Invalid label name: '" . $label . "'");
            }
        }
        $this->labels = $labels;
    }

    public function setLabelsValues(array $labelValues): void
    {
        $this->labelsValues = $labelValues;
    }

    abstract public function getType(): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabelNames(): array
    {
        return $this->labels;
    }

    public function getLabelsValues(): array
    {
        return $this->labelsValues;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    public function getKey(): string
    {
        return sha1($this->getName() . serialize($this->getLabelNames()));
    }

    protected function assertLabelsAreDefinedCorrectly(): void
    {
        if (count($this->labels) !== count($this->labelsValues)) {
            throw new InvalidArgumentException(sprintf('Labels are not defined correctly: %s', print_r($this->labels, true)));
        }
    }
}
