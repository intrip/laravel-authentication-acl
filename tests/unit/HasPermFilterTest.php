<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use Illuminate\Support\Facades\Route;
use Mockery as m;
use App;

class HasPermFilterTest extends TestCase  {

    protected $perm_1 = '_perm1';
    protected $perm_2 = '_perm2';

    public function setUp()
    {
        parent::setUp();
        Route::get('no_perm', function(){return '';});
        Route::get('with_perm', ['middleware' => ["has_perm:{$this->perm_1}"], 'uses' => function(){return '';}]);
        Route::get('with_perms', ['middleware' => ["has_perm:{$this->perm_1},{$this->perm_2}"], 'uses' => function(){return '';}]);
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
     **/
    public function check_for_pemission_if_doesnt_have_route()
    {
        $helper_has_perm = m::mock('StdClass');
        $helper_has_perm->shouldReceive('hasPermission')->with([$this->perm_1])->andReturn(false);
        App::instance('authentication_helper', $helper_has_perm);

        $response = $this->get('with_perm');
        $response->assertStatus(401);
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
 
