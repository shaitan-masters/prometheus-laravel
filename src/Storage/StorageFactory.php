<?php

namespace ShaitanMasters\Prometheus\Storage;

use Illuminate\Support\Facades\Redis;

class StorageFactory
{
    public function getAdapter(): Adapter
    {
        $redis = new Redis(config('prometheus.redis'));

        return new RedisAdapter($redis::connection());
    }
}
