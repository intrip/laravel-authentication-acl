<?php namespace Jacopo\Authentication;

use App;
use InstallCommand;
use Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator;
use Jacopo\Authentication\Classes\CustomProfile\Repository\CustomProfileRepository;
use PrepareCommand;
use TestRunner;
use Config;
use Jacopo\Authentication\Middleware\Config as ConfigMiddleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;
use Jacopo\Authentication\Repository\EloquentPermissionRepository;
use Jacopo\Authentication\Repository\EloquentUserProfileRepository;
use Jacopo\Authentication\Repository\SentryGroupRepository;
use Jacopo\Authentication\Repository\SentryUserRepository;
use Jacopo\Authentication\Services\UserRegisterService;
use Jacopo\Library\Form\FormModel;

class AuthenticationServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
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
        $this->package('jacopo/laravel-authentication-acl');

        $this->bindClasses();

        // include filters
        require __DIR__ . "/../../filters.php";
        // include routes.php
        require __DIR__ . "/../../routes.php";
        // include view composers
        require __DIR__ . "/../../composers.php";
        // include event subscribers
        require __DIR__ . "/../../subscribers.php";
        // include custom validators
        require __DIR__ . "/../../validators.php";

        $this->overwriteSentryConfig();

        $this->setupConnection();

        $this->registerCommands();

        $this->setupAcceptanceTestingParams();
    }

    protected function overwriteSentryConfig()
    {
        $this->app['config']->getLoader()->addNamespace('cartalyst/sentry',
                                                        __DIR__ . '/../../config/sentry');
    }

    protected function bindClasses()
    {
        $this->app->bind('authenticator', function ()
        {
            return new SentryAuthenticator;
        });

        $this->app->bind('Jacopo\Authentication\Interfaces\AuthenticateInterface', function ()
        {
            return $this->app['authenticator'];
        });

        $this->app->bind('authentication_helper', function ()
        {
            return new SentryAuthenticationHelper;
        });

        $this->app->bind('user_repository', function ($app, $config = null)
        {
            return new SentryUserRepository($config);
        });

        $this->app->bind('group_repository', function ()
        {
            return new SentryGroupRepository;
        });

        $this->app->bind('permission_repository', function ()
        {
            return new EloquentPermissionRepository;
        });

        $this->app->bind('profile_repository', function ()
        {
            return new EloquentUserProfileRepository;
        });

        $this->app->bind('register_service', function ()
        {
            return new UserRegisterService;
        });

        $this->app->bind('custom_profile_repository', function ($app, $profile_id = null)
        {
            return new CustomProfileRepository($profile_id);
        });

        $this->app->bind('captcha_validator', function ($app)
        {
            return new GregWarCaptchaValidator();
        });
    }

    protected function loadOtherProviders()
    {
        $this->app->register('Jacopo\Library\LibraryServiceProvider');
        $this->app->register('Cartalyst\Sentry\SentryServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');
    }

    protected function registerAliases()
    {
        AliasLoader::getInstance()->alias("Sentry", 'Cartalyst\Sentry\Facades\Laravel\Sentry');
        AliasLoader::getInstance()->alias("Image", 'Intervention\Image\Facades\Image');
    }

    protected function setupConnection()
    {
        $connection = Config::get('laravel-authentication-acl::database.default');

        if($connection !== 'default')
        {
            $authenticator_conn = Config::get('laravel-authentication-acl::database.connections.' . $connection);
        } else
        {
            $connection = Config::get('database.default');
            $authenticator_conn = Config::get('database.connections.' . $connection);
        }

        Config::set('database.connections.authentication', $authenticator_conn);

        $this->setupPresenceVerifierConnection();
    }

    protected function setupPresenceVerifierConnection()
    {
        $this->app['validation.presence']->setConnection('authentication');
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

    private function registerInstallCommand()
    {
        $this->app['authentication.install'] = $this->app->share(function ($app)
        {
            return new InstallCommand;
        });

        $this->commands('authentication.install');
    }

    private function registerPrepareCommand()
    {
        $this->app['authentication.prepare'] = $this->app->share(function ($app)
        {
            return new PrepareCommand;
        });

        $this->commands('authentication.prepare');
    }

    private function registerCommands()
    {
        $this->registerInstallCommand();
        $this->registerPrepareCommand();
    }

    protected function setupAcceptanceTestingParams()
    {
        if(App::environment() == 'testing-acceptance')
        {
            $this->useMiddlewareCustomConfig();
        }
    }

    protected function useMiddlewareCustomConfig()
    {
        App::instance('config', new ConfigMiddleware());

        Config::swap(new ConfigMiddleware());
    }
}