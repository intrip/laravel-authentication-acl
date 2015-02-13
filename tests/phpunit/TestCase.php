<?php namespace Jacopo\Authentication\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Jacopo\Authentication\Tests\Unit\Stubs\NullLogger;
use \Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Config\EnvironmentVariables;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends OrchestraTestCase {

  // custom environment
  protected $custom_environment = 'testing';

  public function setUp() {
    parent::setUp();
    require_once __DIR__ . "/../../src/routes.php";

    $this->useMailPretend();
    $this->useNullLogger();
  }

  public function useNullLogger() {
    \Mail::setLogger(new NullLogger());
  }

  protected function getPackageProviders() {
    return [
            'Cartalyst\Sentry\SentryServiceProvider',
            'Jacopo\Authentication\AuthenticationServiceProvider',
            'Jacopo\Library\LibraryServiceProvider',
    ];
  }

  protected function getPackageAliases() {
    return [
            'Sentry' => 'Cartalyst\Sentry\Facades\Laravel\Sentry',
    ];
  }

  protected function getNowDateTime() {
    return Carbon::now()->toDateTimeString();
  }

  /**
   * @param $class
   */
  protected function assertHasErrors($class) {
    $this->assertFalse($class->getErrors()->isEmpty());
  }

  protected function useMailPretend() {
    Config::set('mail.pretend', true);
  }

  public function createApplication() {

    $app = new Application;

    $app->detectEnvironment(array(
                                    'local' => array('your-machine-name'),
                            ));

    $app->bindInstallPaths($this->getApplicationPaths());

    $app['env'] = $this->custom_environment;

    $app->instance('app', $app);

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication($app);

    $app->registerCoreContainerAliases();

    with($envVariables = new EnvironmentVariables($app->getEnvironmentVariablesLoader()))->load($app['env']);

    $app->instance('config', $config = new ConfigRepository($app->getConfigLoader(), $app['env']));
    $app->startExceptionHandling();

    date_default_timezone_set($this->getApplicationTimezone());

    $aliases = array_merge($this->getApplicationAliases(), $this->getPackageAliases());
    AliasLoader::getInstance($aliases)->register();

    Request::enableHttpMethodParameterOverride();

    $providers = array_merge($this->getApplicationProviders(), $this->getPackageProviders());
    $app->getProviderRepository()->load($app, $providers);

    $this->getEnvironmentSetUp($app);

    return $app;
  }

  /**
   * @test
   **/
  public function dummy() {
  }

  /**
   * @return mixed
   */
  public function getCustomEnvironment() {
    return $this->custom_environment;
  }

  /**
   * @param mixed $custom_environment
   */
  public function setCustomEnvironment($custom_environment) {
    $this->custom_environment = $custom_environment;

    return $this;
  }
}
 