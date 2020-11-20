<?php

namespace Mojam\Prometheus\Exporters;

use Mojam\Prometheus\Storage\Adapter;
use Mojam\Prometheus\Storage\StorageFactory;

class PrometheusExporter
{
    public const MIME_TYPE = 'text/plain; version=0.0.4';

    private Adapter $storageAdapter;
    private PrometheusRenderTextFormat $reproducer;

    public function __construct(StorageFactory $storageFactory, PrometheusRenderTextFormat $reproducer)
    {
        $this->storageAdapter = $storageFactory->getAdapter();
        $this->reproducer = $reproducer;
    }

    public function export(): string
    {
        $metrics = $this->storageAdapter->collect();

        return $this->reproducer->render($metrics);
    }
}
