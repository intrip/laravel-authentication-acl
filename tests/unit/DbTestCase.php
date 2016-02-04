<?php  namespace LaravelAcl\Authentication\Tests\Unit;

/**
 * Class DbTestCase
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Artisan, DB, Closure, App, Config;
use BadMethodCallException;
use Illuminate\Support\Collection;

class DbTestCase extends TestCase {

    /*
    |--------------------------------------------------------------------------
    | Database connection
    |--------------------------------------------------------------------------
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
        $this->faker = \Faker\Factory::create();
        $this->artisan = $this->app['Illuminate\Contracts\Console\Kernel'];
        $this->setupDbConnection();
    }

    protected function setupDbConnection()
    {
        $this->overrideDefaultConnection();
        $this->getConnectionInfo();
        $this->cleanTables();
        $this->createTestDbSchema();
    }

    protected function getConnectionInfo()
    {
        $this->current_connection = Config::get('database.default');
        $this->connection_info = Config::get("database.connections.{$this->current_connection}");
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
        $this->artisan->call('migrate', ['--path' => 'vendor/jacopo/authentication-sentry/src/migrations']);
        $this->artisan->call('migrate');
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

    protected function overrideDefaultConnection()
    {
        Config::set('database.default', 'sqlite');
    }
}