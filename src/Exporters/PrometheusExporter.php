<?php

namespace ShaitanMasters\Prometheus\Exporters;

use ShaitanMasters\Prometheus\Storage\Adapter;
use ShaitanMasters\Prometheus\Storage\StorageFactory;

class PrometheusExporter
{
    public const MIME_TYPE = 'text/plain; version=0.0.4';

    private Adapter $storageAdapter;
    private PrometheusTextFormatter $formatter;

    public function __construct(StorageFactory $storageFactory, PrometheusTextFormatter $formatter)
    {
        $this->storageAdapter = $storageFactory->getAdapter();
        $this->formatter = $formatter;
    }

    public function export(): string
    {
        $metrics = $this->storageAdapter->collect();

        return $this->formatter->render($metrics);
    }
}
