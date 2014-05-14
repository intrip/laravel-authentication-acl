<?php  namespace Jacopo\Authentication\Tests;
use Jacopo\Authentication\Classes\CustomProfile\Events\ProfilePermissionSubscriber;
use Mockery as m;
use App;
/**
 * Test ProfilePermissionSubscriberTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProfilePermissionSubscriberTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function checkIfHasProfileTypePermission()
    {
        $mock_auth_helper = m::mock('StdClass')
                ->shouldReceive('checkCustomProfileEditPermission')
                ->andReturn(true)
                ->getMock();
        App::instance('authentication_helper', $mock_auth_helper);

        $subscriber = new ProfilePermissionSubscriber();
        $subscriber->checkProfileTypePermission();
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function throwsExceptionIfTypePermissionFails()
    {
        $subscriber = new ProfilePermissionSubscriber();
        $subscriber->checkProfileTypePermission();
    }

}
 