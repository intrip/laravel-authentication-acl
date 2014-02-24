<?php namespace Jacopo\Authentication;

use Illuminate\Support\ServiceProvider;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Illuminate\Foundation\AliasLoader;
use Config;
use Illuminate\Database\Eloquent\Model;

class AuthenticationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 * @override
	 * @return void
	 */
	public function register()
	{
        $this->loadOtherProviders();

        $this->registerAliases();
    }

    /**
     * @override
     */
    public function boot()
    {
        $this->package('jacopo/authentication');

        $this->bindAuthenticator();

        // include filters
        require __DIR__ . "/../../filters.php";
        // include routes.php
        require __DIR__ . "/../../routes.php";
        // include view composers
        require __DIR__ . "/../../composers.php";
        // include event subscribers
        require __DIR__ . "/../../subscribers.php";

        $this->overwriteSentryConfig();
        $this->overwriteWayFormConfig();

        $this->setupConnection();
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
     * @override
	 */
	public function provides()
	{
		return array();
	}

    protected function overwriteSentryConfig()
    {
        $this->app['config']->getLoader()->addNamespace('cartalyst/sentry', __DIR__ . '/../../config/sentry');
    }

    protected function overwriteWayFormConfig()
    {
        $this->app['config']->getLoader()->addNamespace('form', __DIR__ . '/../../config/way-form');
    }

    protected function bindAuthenticator()
    {
        $this->app->bind('authenticator', function ()
        {
            return new SentryAuthenticator;
        });
    }

    protected function loadOtherProviders()
    {
        $this->app->register('Cartalyst\Sentry\SentryServiceProvider');
        $this->app->register('Way\Form\FormServiceProvider');
    }

    protected function registerAliases()
    {
        AliasLoader::getInstance()->alias("Sentry", 'Cartalyst\Sentry\Facades\Laravel\Sentry');
    }

    protected function setupConnection()
    {
        $connection = Config::get('authentication::database.default');

        if ($connection !== 'default')
        {
            $authenticator_conn = Config::get('authentication::database.connections.'.$connection);
        }
        else
        {
            $connection = Config::get('database.default');
            $authenticator_conn = Config::get('database.connections.'.$connection);
        }

        Config::set('database.connections.authentication', $authenticator_conn);

        $this->setupPresenceVerifierConnection();
    }

    protected function setupPresenceVerifierConnection()
    {
        $this->app['validation.presence']->setConnection('authentication');
    }

}