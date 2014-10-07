<?php  namespace Jacopo\Authentication\Tests\Unit; 

use Illuminate\Support\Facades\Route;
use Mockery as m;

class ClientLoggedFilterTest extends TestCase  {

    protected $custom_url = '/custom';

    public function setUp()
    {
        parent::setUp();
        $this->app['router']->enableFilters();
        Route::get('check', ['before' => 'logged', 'uses' => function(){return '';}]);
        Route::get('check_custom', ['before' => "logged:{$this->custom_url}", 'uses' => function(){return '';}]);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function permitAccessToLoggedUsers()
    {
        $this->authCheck(true);

        $this->call('GET', 'check');
    }
    
    /**
     * @test
     **/
    public function redirectToLoginAnonymousUsers()
    {
        $this->authCheck(true);

        $this->call('GET', 'check');

        $this->assertRedirectedTo('/login');
    }
    
    /**
     * @test
     **/
    public function redirectToCustomUrlAnonymousUsers()
    {
        $this->authCheck(true);

        $this->call('GET', 'check_custom');

        $this->assertRedirectedTo($this->custom_url);
    }

    /**
     * @param $true
     */
    private function authCheck($true)
    {
        $auth_success = m::mock('StdClass');
        $auth_success->shouldReceive('check')->andReturn($true);
    }
}
 