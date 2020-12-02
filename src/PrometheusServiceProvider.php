<?php


namespace ShaitanMasters\Prometheus;


use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @param Kernel $kernel
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        $source = realpath(__DIR__ . '/config/config.php');
        $this->publishes([$source => config_path('prometheus.php')]);

        $this->registerMetricsRoute();
    }

    public function config($key, $default = null)
    {
        return config("prometheus.$key", $default);
    }

    public function getConfigInstance($key)
    {
        $instance = $this->config($key);

        if (is_string($instance)) {
            return $this->app->make($instance);
        }

        return $instance;
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'prometheus');
    }

    public function registerMetricsRoute()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
