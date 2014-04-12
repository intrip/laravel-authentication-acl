<?php  namespace Jacopo\Authentication\Tests;
/**
 * Class DbTestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Artisan;
use DB;

class DbTestCase extends TestCase
{
    protected $artisan;

    public function setUp()
    {
        parent::setUp();

        $this->artisan = $this->app->make( 'artisan' );
        $this->createDbSchema();
    }

    /**
     * @test
     **/
    public function it_mock_test()
    {
        $this->assertTrue(true);
    }


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $test_connection = array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        );

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', $test_connection);

    }

    protected function createDbSchema()
    {
        $this->artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations']);
    }
} 