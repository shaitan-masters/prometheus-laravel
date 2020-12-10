##Prometheus Exporter for Laravel
Add endpoint to export simple metrics such as Counter and Gouge.

##Installation

Add to composer

Publish config file

php artisan vendor:publish --provider="ShaitanMasters\Prometheus\PrometheusServiceProvider"

## Usage

    $counter = new  Counter();
    $counter->setNamespace('User');
    $counter->setName('signIn');
    $counter->setHelp('User sign in');
    $counter->inc(); 
