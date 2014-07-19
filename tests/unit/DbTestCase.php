<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Class DbTestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Artisan, DB, Closure, App;
use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory;

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

    /**
     * Uncomment the dbms that you want to use for persistence testing
     */
//        const CURRENT_DBMS = self::PGSQL;
    //    const CURRENT_DBMS = self::MYSQL;
    const CURRENT_DBMS = self::SQLITE;

    /* Connections configurations */
    protected $sqlite_connection = [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
    ];

    protected $pgsql_connection = [
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'authentication_test',
            'username' => 'root',
            'password' => 'root',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',];

    protected $mysql_connection = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'authentication_test',
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
        $this->overwriteDatabasePresenceVerifierForTesting();
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
        $app['path.base'] = __DIR__ . '/../../src';

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
        $this->dropCascadeTables();
    }

    protected function cleanPgsqlTables()
    {
        $this->dropCascadeTables();
    }

    protected function dropCascadeTables()
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

    /**
     * @param       $class_name
     * @param mixed $extra
     * @return array
     */
    protected function make($class_name, $extra = [])
    {
        $created_objs = new Collection();

        while($this->times--)
        {
            $extra_data = ($extra instanceof Closure) ? $extra() : $extra;
            $stub_data = array_merge($this->getModelStub(), $extra_data);
            $created_objs->push($class_name::create($stub_data));
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

    protected function overwriteDatabasePresenceVerifierForTesting()
    {
        App::bindShared('validation.presence', function($app){
            $verifier = new DatabasePresenceVerifier($this->app['db']);
            $verifier->setConnection('testbench');

            return $verifier;
        });

        $validator = App::make('validator');
        $validator->setPresenceVerifier(App::make('validation.presence'));
    }
}