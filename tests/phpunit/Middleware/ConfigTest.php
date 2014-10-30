<?php namespace Jacopo\Authentication\Tests\Unit\Middleware;

use App;
use Exception;
use Jacopo\Authentication\Middleware\Config;
use Jacopo\Authentication\Tests\Unit\DbTestCase;
use Jacopo\Library\Exceptions\NotFoundException;
use Mockery as m;

class ConfigTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function forwardAllCallsToConfig()
    {
        $param_1 = 'test-param-1';
        $param_2 = 'test-param-2';

        $config_get = m::mock('StdClass');
        $method_name = 'get';
        $config_get->shouldReceive($method_name)
                   ->once()
                   ->with($param_1, $param_2);
        App::instance('config', $config_get);

        $config = new Config;
        $repository = m::mock('Jacopo\Authentication\Middleware\Interfaces\ConfigRepositoryInterface');
        $repository->shouldReceive('getOption')
                   ->with($param_1)
                   ->andThrow(new Exception("getOption should not be called"));
        $config->setRepository($repository);

        call_user_func_array([$config, 'get'], [$param_1, $param_2]);
    }

    /**
     * @test
     **/
    public function doesntForwardGetCall_AndReturnHisStatus_IfTheParamIsOverwritten()
    {
        $field = 'field';
        $value = 'value';
        $config = new Config;

        $repository = m::mock('Jacopo\Authentication\Middleware\Interfaces\ConfigRepositoryInterface');
        $repository->shouldReceive('setOption')
                   ->once()
                   ->with($field, $value)
                   ->shouldReceive('getOption')
                   ->once()
                   ->with($field)
                   ->andReturn($value);
        $config->setRepository($repository);

        $config->override($field, $value);

        $this->assertEquals($value, $config->get($field));
    }

    /**
     * @test
     **/
    public function canBeUsedAsArray()
    {
        $value = 'test-value';
        $key = 'test-key';
        $config = new Config;

        $config[$key] = $value;

        $this->assertEquals($value, $config[$key]);
    }
}
 