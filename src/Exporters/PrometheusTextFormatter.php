<?php

namespace ShaitanMasters\Prometheus\Exporters;

use ShaitanMasters\Prometheus\Dto\Metric;
use ShaitanMasters\Prometheus\Dto\MetricCollection;

class PrometheusTextFormatter
{
    public function render(array $metrics): string
    {
        usort($metrics, function (MetricCollection $a, MetricCollection $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $lines = [];

        foreach ($metrics as $metricCollection) {
            $lines[] = '# HELP ' . $metricCollection->getName() . " {$metricCollection->getHelp()}";
            $lines[] = '# TYPE ' . $metricCollection->getName() . " {$metricCollection->getType()}";

            foreach ($metricCollection->getMetrics() as $metric) {
                $lines[] = $this->renderMetric($metricCollection, $metric);
            }
        }

        return implode("\n", $lines) . "\n";
    }

    private function renderMetric(MetricCollection $metricCollection, Metric $metric): string
    {
        $escapedLabels = [];

        $labelNames = $metricCollection->getLabelNames();

        if ($metricCollection->hasLabelNames() || $metric->hasLabelNames()) {
            $labels = array_combine(array_merge($labelNames, $metric->getLabelNames()), $metric->getLabelValues());

            foreach ($labels as $labelName => $labelValue) {
                $escapedLabels[] = $labelName . '="' . $this->escapeLabelValue($labelValue) . '"';
            }

            return $metric->getName() . '{' . implode(',', $escapedLabels) . '} ' . $metric->getValue();
        }

        return $metric->getName() . ' ' . $metric->getValue();
    }

    private function escapeLabelValue(string $value): string
    {
        return str_replace(['\\', "\n", '"'], ['\\\\', '\\n', '\\"'], $value);
    }
}
