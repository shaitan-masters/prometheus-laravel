
## Configuration

php artisan vendor:publish --provider="Mojam\Prometheus\PrometheusServiceProvider"


## Usage

    $counter = new  Counter();
    $counter->setNamespace('User');
    $counter->setName('signIn');
    $counter->setHelp('User sign in');
    $counter->inc(); 
