<?php  namespace Jacopo\Authentication\Tests; 

use Jacopo\Authentication\Models\User;
use Mockery as m;
use App;

class DashboardControllerTest extends DbTestCase  {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canShowDashboardPage()
    {
        $mock_authenticator = m::mock('StdClass');
        $mock_authenticator->shouldReceive('getLoggedUser')->andReturn(new User());
        App::instance('authenticator', $mock_authenticator);

        $this->route('GET', 'dashboard.default');

        $this->assertResponseOk();
    }
}
 