<?php  namespace Jacopo\Authentication\Tests;

use Illuminate\Support\Facades\DB;
use Jacopo\Authentication\Helpers\DbHelper;
use Mockery as m;

class DbHelperTest extends DbTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canStartTransaction()
    {
        $mock_transaction = m::mock('StdClass');
        $mock_transaction->shouldReceive('beginTransaction')->once();
        $mock_getpdo = m::mock('StdClass');
        $mock_getpdo->shouldReceive('getPdo')->once()->andReturn($mock_transaction);
        DB::shouldReceive('connection')->once()
            ->with(DbHelper::getConnectionName())
            ->andReturn($mock_getpdo);

        //@todo use 2 test one that save data with commit
        // and one that doesnt save data

        DbHelper::startTransaction();
    }

    /**
     * @test
     **/
    public function canStopForeignKeysCheckIfMysql()
    {
        //@todo go from here then go in userController
        //@todo use mocking here to be sure he fix foreign keys
        DbHelper::startTransaction();

    }
}
 