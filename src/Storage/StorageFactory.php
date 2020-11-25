<?php

namespace Mojam\Prometheus\Storage;

use Illuminate\Support\Facades\Redis;

class StorageFactory
{
    public function getAdapter(): Adapter
    {
        $redis = new Redis(config('prometheus_exporter.redis'));

        return new RedisAdapter($redis::connection());
    }
}
