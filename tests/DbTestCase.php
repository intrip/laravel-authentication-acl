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
        $this->populateDB();
    }

    /**
     * @test
     **/
    public function it_mock_test()
    {
        $this->assertTrue(true);
    }

    /**
     * @deprecated use sqlite instead
     */
    protected function cleanDb()
    {
        $manager = DB::getDoctrineSchemaManager();
        $tables = $manager->listTableNames();
        foreach ($tables as $key => $table) {
            DB::Statement("DROP TABLE ".$table."");
        }
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

    protected function populateDB()
    {
        $this->artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations']);
    }
} 