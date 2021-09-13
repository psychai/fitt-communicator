<?php

namespace Psychai\FittCommunicator;

use Psychai\FittCommunicator\Middleware\CallbackMiddleware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'fitt-communicator');

        $this->app->singleton(FittCommunicator::class, function ($app) {
            $options['app.env'] = $app['config']->get('app')['env'] ?? null;
            $options['app.debug'] = $app['config']->get('app')['debug'] ?? null;
            $options['fitt-communicator'] = $app['config']->get('fitt-communicator');

            return new FittCommunicator($options);
        });

        $this->app['router']->aliasMiddleware('fitt-communicator:callback-middleware', CallbackMiddleware::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/fitt-communicator.php');

        $this->publishes([
            $this->configPath() => config_path('fitt-communicator.php'),
        ]);
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/fitt-communicator.php';
    }
}
