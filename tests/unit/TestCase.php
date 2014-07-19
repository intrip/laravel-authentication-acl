<?php namespace Jacopo\Authentication\Tests\Unit;

use Carbon\Carbon;
use \Orchestra\Testbench\TestCase as OrchestraTestCase;
/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends OrchestraTestCase  {

    public function setUp()
    {
        parent::setUp();

        require_once __DIR__ . "/../../src/routes.php";
    }

    protected function getPackageProviders()
    {
        return [
                'Cartalyst\Sentry\SentryServiceProvider',
                'Jacopo\Authentication\AuthenticationServiceProvider',
                'Jacopo\Library\LibraryServiceProvider',
            ];
    }

    protected function getPackageAliases()
    {
        return [
            'Sentry' => 'Cartalyst\Sentry\Facades\Laravel\Sentry',
        ];
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
}
 