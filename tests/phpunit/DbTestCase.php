<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Class DbTestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Artisan, DB, Closure, App, Config;
use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory;

class DbTestCase extends TestCase {
    /*
      |--------------------------------------------------------------------------
      | Database connection
      |--------------------------------------------------------------------------
      | Edit the file "packages/jacopo/laravel-authentication-acl/config/testing/database.php"
    | to change the database connection for the tests.
    |
    */
    protected $connection_info;
    protected $current_connection;

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
        $this->setupDbConnection();
        $this->overwriteDatabasePresenceVerifierForTesting();
    }

    protected function setupDbConnection()
    {
        $this->getConnectionInfo();
        $this->overrideCurrentConnection();
        $this->cleanTables();
        $this->createTestDbSchema();
    }

    protected function getConnectionInfo()
    {
        $this->current_connection = Config::get('laravel-authentication-acl::database.default');
        $this->connection_info = Config::get("laravel-authentication-acl::database.connections.{$this->current_connection}");
    }

    protected function overrideCurrentConnection()
    {
        $this->app['config']->set('database.default', 'testbench');
        $this->app['config']->set('database.connections.testbench', $this->connection_info);
    }

    protected function cleanTables()
    {
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
    }

    /**
     * @return mixed
     */
    protected function callCleanMethod()
    {
        return $this->{"clean" . $this->current_connection . "Tables"}();
    }

    protected function cleanmysqlTables()
    {
        $this->dropCascadeTables();
    }

    protected function cleanpgsqlTables()
    {
        $this->dropCascadeTables();
    }

    protected function cleansqliteTables()
    {
        // do nothing
    }

    protected function cleansqlitedbTables()
    {
        $manager = DB::getDoctrineSchemaManager();
        $tables = $manager->listTableNames();

        foreach($tables as $table)
        {
            DB::Statement("DROP TABLE " . $table);
        }
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

        // test custom config data for acceptance testsuite
        $this->artisan->call('migrate', ["--database" => "testbench", '--path' => '../src/migrations_test']);
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
        App::bindShared('validation.presence', function ($app)
        {
            $verifier = new DatabasePresenceVerifier($this->app['db']);
            $verifier->setConnection('testbench');

            return $verifier;
        });

        $validator = App::make('validator');
        $validator->setPresenceVerifier(App::make('validation.presence'));
    }
}