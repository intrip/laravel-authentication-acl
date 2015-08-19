<?php namespace LaravelAcl\Authentication\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Queue\NullQueue;
use Illuminate\Support\Facades\Mail;
use LaravelAcl\Authentication\Tests\Unit\Stubs\NullLogger;
use Illuminate\Config\EnvironmentVariables;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends LaravelTestCase {

    protected $custom_environment;
    // Laravel 5.1 upgrade
    protected $baseUrl = 'http://localhost';

    public function setUp()
    {
        parent::setUp();
        $this->useNullLogger();
    }

    public function useNullLogger()
    {
        Mail::setLogger(new NullLogger());
        Mail::pretend();
    }

    protected function getNowDateTime()
    {
        return Carbon::now()->toDateTimeString();
    }

    /**
     * @param $class
     */
    protected function assertHasErrors($class)
    {
        $this->assertFalse($class->getErrors()->isEmpty());
    }

    /**
     * @return mixed
     */
    public function getCustomEnvironment()
    {
        return $this->custom_environment;
    }

    /**
     * @param mixed $custom_environment
     */
    public function setCustomEnvironment($custom_environment)
    {
        $this->custom_environment = $custom_environment;

        return $this;
    }


    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        return $app;
    }
}
 