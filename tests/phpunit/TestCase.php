<?php namespace Jacopo\Authentication\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Jacopo\Authentication\Tests\Unit\Stubs\NullLogger;
use \Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends OrchestraTestCase {

  // custom environment
  protected $custom_environment;

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
    $app = parent::createApplication();
    if($this->custom_environment){
      $app['env'] = $this->custom_environment;
    }

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
 