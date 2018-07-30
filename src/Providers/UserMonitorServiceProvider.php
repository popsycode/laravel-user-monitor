<?php
/**
 * Created by PhpStorm.
 * User: POPsy
 * Date: 30.07.2018
 * Time: 20:57
 */

namespace POPsy\UserMonitor\Providers;


use Illuminate\Support\ServiceProvider;
use POPsy\UserMonitor\Monitor;

class UserMonitorServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => app()->basePath() . '/config/user-monitor.php',

        ], 'user-monitor');
        include __DIR__.'/../routes.php';

        $this->registerProviders();
        $this->mergeConfig();

        $this->app->bind('user-monitor', function ($app) {
            return new Monitor($app);
        });
        $this->app->alias('user-monitor', 'POPsy\UserMonitor\Monitor');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }



    public function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * Merges user's and entrust's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'user-monitor'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }

}