<?php

namespace ShaitanMasters\Prometheus\Http;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use ShaitanMasters\Prometheus\Exporters\PrometheusExporter;

class MetricsController extends Controller
{
    protected ResponseFactory $responseFactory;

    protected PrometheusExporter $exporter;

    public function __construct(ResponseFactory $responseFactory, PrometheusExporter $exporter)
    {
        $this->responseFactory = $responseFactory;
        $this->exporter = $exporter;
    }

    public function __invoke(): Response
    {
        $metrics = $this->exporter->export();

        return $this->responseFactory->make($metrics, Response::HTTP_OK, ['Content-Type' => PrometheusExporter::MIME_TYPE]);
    }
}
