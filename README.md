
## Configuration

php artisan vendor:publish --provider="Valentin\Mojam\PrometheusServiceProvider"

## Usage

    $counter = new  Counter();
    $counter->setNamespace('User');
    $counter->setName('signIn');
    $counter->setHelp('User sign in');
    $counter->inc(); 
