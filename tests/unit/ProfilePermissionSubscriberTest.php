<?php  namespace Jacopo\Authentication\Tests\Unit;
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
        $has_edit_profile_permssion = m::mock('StdClass')
                ->shouldReceive('checkCustomProfileEditPermission')
                ->andReturn(true)
                ->getMock();
        App::instance('authentication_helper', $has_edit_profile_permssion);

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
 