<?php namespace LaravelAcl\Authentication;

use App;
use LaravelAcl\Authentication\Classes\Captcha\GregWarCaptchaValidator;
use LaravelAcl\Authentication\Classes\CustomProfile\Repository\CustomProfileRepository;
use LaravelAcl\Authentication\Commands\InstallCommand;
use Config;
use LaravelAcl\Authentication\Helpers\FileRouteHelper;
use LaravelAcl\Authentication\Middleware\Config as ConfigMiddleware;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use LaravelAcl\Authentication\Classes\SentryAuthenticator;
use LaravelAcl\Authentication\Helpers\SentryAuthenticationHelper;
use LaravelAcl\Authentication\Repository\EloquentPermissionRepository;
use LaravelAcl\Authentication\Repository\EloquentUserProfileRepository;
use LaravelAcl\Authentication\Repository\SentryGroupRepository;
use LaravelAcl\Authentication\Repository\SentryUserRepository;
use LaravelAcl\Authentication\Services\UserRegisterService;

class AuthenticationServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $providers = [
            \LaravelAcl\Library\LibraryServiceProvider::class,
            \Cartalyst\Sentry\SentryServiceProvider::class,
            \Intervention\Image\ImageServiceProvider::class,
            \Collective\Html\HtmlServiceProvider::class
    ];

    protected $aliases = [
            "Sentry" => \Cartalyst\Sentry\Facades\Laravel\Sentry::class,
            "Image"  => \Intervention\Image\Facades\Image::class,
            'Form'   => \Collective\Html\FormFacade::class,
            'Html'   => \Collective\Html\HtmlFacade::class,
            'HTML'   => \Collective\Html\HtmlFacade::class
    ];

    /**
     * Register the service provider.
     *
     * @override
     * @return void
     */
    public function register()
    {
        $this->loadProvidersDependency();
        $this->registerAliases();
    }

    /**
     * @override
     */
    public function boot()
    {
        $this->bindClasses();

        // setup views path
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laravel-authentication-acl');
        // include view composers
        require __DIR__ . "/composers.php";
        // include event subscribers
        require __DIR__ . "/subscribers.php";
        // include custom validators
        require __DIR__ . "/validators.php";
        // package routes
        require __DIR__ . '/../Http/routes.php';
        // other bootstrap commands
        require __DIR__ . '/bootstrap.php';

        $this->registerCommands();

        $this->setupPublishDataCommand();

        $this->overwriteSentryConfig();
    }

    protected function overwriteSentryConfig()
    {
        Config::set('cartalyst.sentry', Config::get('acl_sentry'));
    }

    protected function bindClasses()
    {
        $this->app->bind('authenticator', function ()
        {
            return new SentryAuthenticator;
        });

        $this->app->bind('LaravelAcl\Authentication\Interfaces\AuthenticateInterface', function ()
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

        $this->app->bind('route_perm_helper', function ($app)
        {
            return new FileRouteHelper();
        });
    }

    protected function loadProvidersDependency()
    {
        foreach($this->providers as $provider)
        {
            $this->app->register($provider);
        }
    }

    protected function registerAliases()
    {
        foreach($this->aliases as $alias => $original)
        {
            AliasLoader::getInstance()->alias($alias, $original);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     * @override
     */
    public function provides()
    {
        return $this->providers;
    }

    private function registerInstallCommand()
    {
        $this->app->singleton('authentication.install', function ($app)
        {
            return new InstallCommand;
        });

        $this->commands('authentication.install');
    }

    private function registerCommands()
    {
        $this->registerInstallCommand();
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


    protected function setupPublishDataCommand()
    {
        $this->publishAssets();
        $this->publishConfig();
        $this->publishViews();
        $this->publishMigrations();
    }

    protected function publishAssets()
    {
        $this->publishes([
                                 __DIR__ . '/../../public/packages/jacopo/laravel-authentication-acl' => public_path('packages/jacopo/laravel-authentication-acl'),
                         ]);

    }

    protected function publishConfig()
    {
        $this->publishes([
                                 __DIR__ . '/../../config/acl_base.php'        => config_path('acl_base.php'),
                                 __DIR__ . '/../../config/acl_menu.php'        => config_path('acl_menu.php'),
                                 __DIR__ . '/../../config/acl_permissions.php' => config_path('acl_permissions.php'),
                                 __DIR__ . '/../../config/acl_messages.php'    => config_path('acl_messages.php'),
                                 __DIR__ . '/../../config/acl_sentry.php'      => config_path('acl_sentry.php'),
                         ]);
    }

    protected function publishViews()
    {

        $this->publishes([
                                 __DIR__ . '/../../resources/views' => base_path('resources/views/vendor/laravel-authentication-acl'),
                         ]);
    }

    protected function publishMigrations()
    {

        $this->publishes([
                                 __DIR__ . '/../../database/migrations' => $this->app->databasePath() . '/migrations',
                         ]);
    }
}
