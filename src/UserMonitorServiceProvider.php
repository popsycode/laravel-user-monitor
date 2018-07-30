<?php
/**
 * Created by PhpStorm.
 * User: POPsy
 * Date: 30.07.2018
 * Time: 20:57
 */

namespace POPsy\UserMonitor;


use Illuminate\Support\ServiceProvider;

class UserMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/config.php' => app()->basePath() . '/config/proxy-image.php',
        ], 'user-monitor');

        include __DIR__.'/routes.php';

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerProxyImage();
        $this->mergeConfig();
        $this->loadAssets();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerProxyImage()
    {
        $this->app->bind('user-monitor', function ($app) {
            return new Monitor($app);
        });
        $this->app->alias('user-monitor', 'POPsy\UserMonitor\Monitor');
    }

    /**
     * Load assets.
     */
    protected function loadAssets()
    {
        /*$this->publishes([
            __DIR__.'/../public' => public_path('vendor'),
        ], 'proxy-image');*/
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
}