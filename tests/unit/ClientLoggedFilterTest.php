<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use Illuminate\Support\Facades\Route;
use Mockery as m;

class ClientLoggedFilterTest extends TestCase  {

    protected $custom_url = '/custom';

    public function setUp()
    {
        parent::setUp();
        Route::get('check', ['middleware' => 'admin_logged', 'uses' => function(){return '';}]);
        Route::get('check_custom', ['middleware' => "admin_logged:{$this->custom_url}", 'uses' => function(){return '';}]);
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

        $response = $this->get('check');

        $response->assertRedirect('/login');
    }

    /**
     * @test
     **/
    public function redirectToCustomUrlAnonymousUsers()
    {
        $this->authCheck(true);

        $response = $this->call('GET', 'check_custom');

        $response->assertRedirect($this->custom_url);
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
 
