<?php namespace mahbubcsedu\laraturksoap;
/**
 * Created by PhpStorm.
 * User: mahbub
 * Date: 7/28/15
 * Time: 2:36 AM
 */
use Illuminate\Support\ServiceProvider;

class TurkSoapServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/turksopa.php';

        $config = [ $configPath => config_path('turksoap.php') ];

        $this->publishes( $config );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('turksoap', function ($app) {
            return new AmazonMechanicalTurk;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('turksoap');
    }

}