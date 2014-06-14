<?php  namespace Jacopo\Authentication\Tests;

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
    }

    /**
     * @test
     **/
    public function canStartTransactionAndCommit()
    {
        DbHelper::startTransaction();
        $this->model_stub->create([]);
        DbHelper::commit();
        $this->assertEquals(1, $this->model_stub->get()->count());
    }

    /**
     * @test
     **/
    public function canStartAndRollbackTransaction()
    {
        DbHelper::startTransaction();
        $this->model_stub->create([]);
        DbHelper::rollback();
        $this->assertEquals(0, $this->model_stub->get()->count());
    }

    /**
     * @test
     **/
    public function canStopForeignKeysCheckIfSupported()
    {
        DbHelperStub::$current_driver_name = '';
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
        DbHelperStub::$current_driver_name = '';
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