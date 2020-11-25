<?php

namespace Valentin\Mojam\Http;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Valentin\Mojam\Exporters\PrometheusExporter;

class MetricsController extends Controller
{
    protected ResponseFactory $responseFactory;

    protected PrometheusExporter $exporter;

    public function __construct(ResponseFactory $responseFactory, PrometheusExporter $exporter)
    {
        $this->responseFactory = $responseFactory;
        $this->exporter = $exporter;
    }

    /**
     * Get Prometheus data.
     *
     * @return Response
     *
     * @OA\Get (
     *      path="/prometheus/metrics",
     *      tags={"Prometheus"},
     *      summary="Prometheus endpoint .",
     *      operationId="prometheus",
     *
     *      @OA\Response (
     *          response=200,
     *          description="HTTP 200 OK",
     *      ),
     *  )
     */
    public function __invoke(): Response
    {
        $metrics = $this->exporter->export();

        return $this->responseFactory->make($metrics, 200, ['Content-Type' => PrometheusExporter::MIME_TYPE]);
    }
}
