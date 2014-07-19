<?php  namespace Jacopo\Authentication\Tests\Unit;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Jacopo\Authentication\Helpers\DbHelper;
use Jacopo\Authentication\Models\BaseModel;
use Mockery as m;

class DbHelperTest extends DbTestCase
{
    protected $model_stub;

    public function setUp()
    {
        parent::setUp();

        $this->model_stub = new TransactionStub;
        $this->model_stub->createSchema();
    }

    public function tearDown()
    {
        m::close();
        $this->resetStaticDataToDefault();
    }

    /**
     * @test
     **/
    public function canStartTransactionCommitAndStopForeignKeysCheck()
    {
        DbHelperForeignKeysStub::startTransaction();
        $this->model_stub->create([]);
        DbHelperForeignKeysStub::commit();

        $this->assertEquals(1, $this->model_stub->get()->count());
        $this->assertTrue(DbHelperForeignKeysStub::$foreign_keys_started);
        $this->assertTrue(DbHelperForeignKeysStub::$foreign_keys_stopped);
    }

    /**
     * @test
     **/
    public function canStartTransactionRollbackAndStopForeignKeysCheck()
    {
        DbHelperForeignKeysStub::startTransaction();
        $this->model_stub->create([]);
        DbHelperForeignKeysStub::rollback();

        $this->assertEquals(0, $this->model_stub->get()->count());
        $this->assertTrue(DbHelperForeignKeysStub::$foreign_keys_started);
        $this->assertTrue(DbHelperForeignKeysStub::$foreign_keys_stopped);
    }

    /**
     * @test
     **/
    public function canStopForeignKeysCheckIfSupported()
    {
        $mock_exec = m::mock('StdClass')
            ->shouldReceive('exec')
            ->once()
            ->with('SET FOREIGN_KEY_CHECKS=0;')
            ->getMock();
        $mock_getPdo = m::mock('StdClass')
            ->shouldReceive('getPdo')
            ->once()
            ->andReturn($mock_exec)
            ->getMock();
        DB::shouldReceive('connection')
            ->once()
            ->andReturn($mock_getPdo);
        DbHelperStub::stopForeignKeysCheck();
    }

    /**
     * @test
     **/
    public function itDoesntStopForeignKeysCheckIfNotSupported()
    {
        DbHelperStub::$current_driver_name = 'sqlite';
        DbHelperStub::stopForeignKeysCheck();
    }

    /**
     * @test
     **/
    public function canStartForeignKeysCheckIfSupported()
    {
        $mock_exec = m::mock('StdClass')
            ->shouldReceive('exec')
            ->once()
            ->with('SET FOREIGN_KEY_CHECKS=1;')
            ->getMock();
        $mock_getPdo = m::mock('StdClass')
            ->shouldReceive('getPdo')
            ->once()
            ->andReturn($mock_exec)
            ->getMock();
        DB::shouldReceive('connection')
            ->once()
            ->andReturn($mock_getPdo);
        DbHelperStub::startForeignKeysCheck();
    }

    protected function resetStaticDataToDefault()
    {
        DbHelperForeignKeysStub::$foreign_keys_started = false;
        DbHelperForeignKeysStub::$foreign_keys_stopped = false;
        DbHelperStub::$current_driver_name = 'mysql';
    }
}

class TransactionStub extends BaseModel
{
    protected $table = 'trans_stub';

    public function createSchema()
    {
        Schema::create('trans_stub', function ($table) {
            $table->increments('id');
            $table->timestamps();
        });
    }
}

class DbHelperStub extends DbHelper
{
    public static $current_driver_name = '';

    protected static function getCurrentDriverName()
    {
        return static::$current_driver_name;
    }
}

class DbHelperForeignKeysStub extends DbHelperStub
{
    public static $foreign_keys_started;
    public static $foreign_keys_stopped;

    public static function startForeignKeysCheck()
    {
        static::$foreign_keys_started = true;
    }

    public static function stopForeignKeysCheck()
    {
        static::$foreign_keys_stopped = true;

    }
}