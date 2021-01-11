<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Redis connection
    |--------------------------------------------------------------------------
    |
    | Redis connection params
    */
    'redis' => [
            'url' => '',
            'host' =>  '127.0.0.1',
            'password' =>  null,
            'port' =>  '6379',
            'database' =>  '2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route URI for prometheus
    |--------------------------------------------------------------------------
    |
    | This route is used by prometheus to grab metrics
    */
    'route_uri' => 'metrics',

    /*
    |--------------------------------------------------------------------------
    | Security middleware
    |--------------------------------------------------------------------------
    |
    | Do you want to use security middleware
    | default: false
    */
    'use_security_middleware' => true,

    /*
    |--------------------------------------------------------------------------
    | API token
    |--------------------------------------------------------------------------
    |
    | This token is used by prometheus to secure
    | Bearer Token auth
    */
    'api_token' => 'prometheus-api-key-put-here',

    /*
    |--------------------------------------------------------------------------
    | Delete metrics after scrap
    |--------------------------------------------------------------------------
    |
    | Metrics from this array will be deleted from the redis after the scrap
    */
    'metrics_to_delete' => []
];
