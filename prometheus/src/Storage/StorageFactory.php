<?php

namespace Mojam\Prometheus;

use Illuminate\Support\Facades\Redis;

class StorageFactory
{
    public function getAdapter(): Adapter
    {
        $connection = Redis::connection('prometheus');

        return new RedisAdapter($connection);
    }
}
