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
    | Do not delete metrics after scrap
    |--------------------------------------------------------------------------
    |
    | Metrics from this array do not deleted from the redis after the scrap
    | Example: 'metrics_do_not_delete' => ['first_metric_name', 'another_metric_name']
    */
    'metrics_do_not_delete' => []
];
