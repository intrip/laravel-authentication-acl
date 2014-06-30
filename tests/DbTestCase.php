<?php  namespace Jacopo\Authentication\Tests;

/**
 * Class DbTestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Artisan;
use BadMethodCallException;
use Carbon\Carbon;
use DB;

class DbTestCase extends TestCase
{

    /*
	|--------------------------------------------------------------------------
	| DBMS for tests
	|--------------------------------------------------------------------------
	| Here you can configure the dbms for the test environment
    |
    */
    const SQLITE = "Sqlite";
    const MYSQL = "Mysql";
    const PGSQL = "Pgsql";

    const CURRENT_DBMS = self::PGSQL;
    //    const CURRENT_DBMS = self::MYSQL;
    //    const CURRENT_DBMS = self::SQLITE;

    /* Connections configurations */
    protected $sqlite_connection = [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
    ];

    protected $pgsql_connection = [
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'authentication',
            'username' => 'root',
            'password' => 'root',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',];

    protected $mysql_connection = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'authentication',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'prefix'    => '',
            'collation' => 'utf8_unicode_ci',
            'schema'    => 'public',];

    /*
	|--------------------------------------------------------------------------
	| Other
	|--------------------------------------------------------------------------
    |
    */

    // used for iterative make
    protected $times = 1;

    protected $artisan;
    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->artisan = $this->app->make('artisan');
        $this->faker = \Faker\Factory::create();

        $this->cleanTables();
        $this->createTestDbSchema();
    }

    protected function cleanTables()
    {
        if(self::CURRENT_DBMS == static::SQLITE) return;
        $this->callCleanMethod();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'testbench');

        $connection_config = $this->{strtolower(self::CURRENT_DBMS) . "_connection"};
        $app['config']->set('database.connections.testbench', $connection_config);
    }

    /**
     * @return mixed
     */
    protected function callCleanMethod()
    {
        return $this->{"clean" . self::CURRENT_DBMS . "Tables"}();
    }

    protected function cleanMysqlTables()
    {
        $manager = DB::getDoctrineSchemaManager();
        $tables = $manager->listTableNames();

        DB::Statement("SET  FOREIGN_KEY_CHECKS=0");
        foreach($tables as $table)
        {
            DB::Statement("DROP TABLE " . $table . " CASCADE");
        }
        DB::Statement("SET FOREIGN_KEY_CHECKS=1");
    }

    protected function cleanPgsqlTables()
    {
        $manager = DB::getDoctrineSchemaManager();
        $tables = $manager->listTableNames();

        foreach($tables as $table)
        {
            DB::Statement("DROP TABLE " . $table . " CASCADE");
        }
    }

    protected function createTestDbSchema()
    {
        $this->artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations']);
    }

    protected function make($class_name, $fields = [])
    {
        $created_objs = [];

        while($this->times--)
        {
            $stub_data = array_merge($this->getModelStub(), $fields);
            $created_objs[] = $class_name::create($stub_data);
        }

        $this->resetTimes();

        return $created_objs;
    }

    protected function getModelStub()
    {
        throw new BadMethodCallException("You need to implement getModelStub method in your own test class.");
    }

    protected function times($count)
    {
        $this->times = $count;

        return $this;
    }

    protected function resetTimes()
    {
        $this->times = 1;
    }

    protected function assertObjectHasAllAttributes(array $attributes, $object, array $except = [])
    {
        $this->objectHasAllArrayAttributes($attributes, $object, $except);
    }

    protected function objectHasAllArrayAttributes(array $attributes, $object, array $except = [])
    {
        foreach($attributes as $key => $value)
        {
            if(!in_array($key, $except)) $this->assertEquals($value, $object->$key);
        }
    }

    protected function getNowDateTime()
    {
        return Carbon::now()->toDateTimeString();
    }
}