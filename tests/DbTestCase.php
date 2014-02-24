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
    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make( 'artisan' );

        $this->cleanDb();
        $this->populateDB($artisan);
    }

    /**
     * @test
     **/
    public function it_mock_test()
    {
        $this->assertTrue(true);
    }

    protected function cleanDb()
    {
        $manager = DB::getDoctrineSchemaManager();
        $tables = $manager->listTableNames();

        DB::Statement("SET FOREIGN_KEY_CHECKS=0");
        foreach ($tables as $key => $table) {
            DB::Statement("DROP TABLE ".$table."");
        }
        DB::Statement("SET FOREIGN_KEY_CHECKS=1");
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

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'authentication_test',
                        'username'  => 'root',
                        'password'  => 'root',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
        ));
    }

    /**
     * @param $artisan
     */
    protected function populateDB($artisan)
    {
        $artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations', '--seed' => '']);
    }
} 