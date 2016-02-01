<?php namespace LaravelAcl\Authentication\Tests\Unit;

use Carbon\Carbon;
/**
 * Test TestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

    protected $baseUrl = 'http://localhost';

    public function setUp()
    {
        parent::setUp();
        $this->useNullLogger();
    }

    public function useNullLogger()
    {
//        Mail::setLogger(new NullLogger());
//        Mail::pretend();

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
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $app['env'] = getenv('APP_ENV');

        return $app;
    }
}
 