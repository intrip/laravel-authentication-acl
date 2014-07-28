<?php  namespace Jacopo\Authentication\Tests\Unit; 

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Mockery as m;
use App;

class HasPermFilterTest extends TestCase  {

    protected $perm_1 = '_perm1';
    protected $perm_2 = '_perm2';

    public function setUp()
    {
        parent::setUp();
        $this->app['router']->enableFilters();
        Route::get('no_perm', function(){return '';});
        Route::get('with_perm', ['before' => ["has_perm:{$this->perm_1}"], 'uses' => function(){return '';}]);
        Route::get('with_perms', ['before' => ["has_perm:{$this->perm_1},{$this->perm_2}"], 'uses' => function(){return '';}]);

    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function showRouteWithNoPermission()
    {
        $this->call('GET','no_perm');
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     **/
    public function doesntShowRouteIfDoesntHavePerm()
    {
        $helper_has_perm = m::mock('StdClass');
        $helper_has_perm->shouldReceive('hasPermission')->with([$this->perm_1])->andReturn(false);
        App::instance('authentication_helper', $helper_has_perm);

        $this->call('GET','with_perm');
    }
    
    /**
     * @test
     **/
    public function showRouteIfHasPerm()
    {
        $helper_has_perm = m::mock('StdClass');
        $helper_has_perm->shouldReceive('hasPermission')->with([$this->perm_1])->andReturn(true);
        App::instance('authentication_helper', $helper_has_perm);

        $this->call('GET','with_perm');
    }
    
    /**
     * @test
     **/
    public function handleMultiplePermSeparatedByComma()
    {
        $helper_has_perm = m::mock('StdClass');
        $helper_has_perm->shouldReceive('hasPermission')->with([$this->perm_1, $this->perm_2])->andReturn(true);
        App::instance('authentication_helper', $helper_has_perm);

        $this->call('GET','with_perms');
    }

}
 