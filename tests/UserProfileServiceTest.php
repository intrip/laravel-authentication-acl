<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Services\UserProfileService;
use Jacopo\Library\Exceptions\ValidationException;
use Jacopo\Library\Validators\AbstractValidator;
use Mockery as m;
use App;
/**
 * Test UserProfileServiceTest
 *
 * @author jacopo beschi j.beschi@palmabit.com
 */
class UserProfileServiceTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_create_a_profile()
    {
        $mock_form_profile_success = m::mock('Jacopo\Library\Form\FormModel')->shouldReceive('process')->once()->andReturn(true)->getMock();

        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);

        $service->processForm([]);
    }

    /**
     * @test
     * @expectedException \Jacopo\Library\Exceptions\InvalidException
     **/
    public function it_throw_exception_if_cannot_process()
    {
        $mock_form_profile = m::mock('Jacopo\Library\Form\FormModel');
        $mock_form_profile->shouldReceive('process')->once()->andThrow(new ValidationException);
        $mock_form_profile->shouldReceive('getErrors')->once()->andReturn(["error"]);

        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile);

        $service->processForm([]);
        $this->assertEquals("error", $service->getErrors());
    }

    /**
     * @test
     **/
    public function it_update_user_password_if_given()
    {
        $mock_form_profile_success = m::mock('Jacopo\Library\Form\FormModel')->shouldReceive('process')->andReturn(true)->getMock();
        // mock user repository
        $mock_user_repo = m::mock('StdClass')->shouldReceive('update')->once()->andReturn(true)->getMock();
        App::instance('user_repository',$mock_user_repo);
        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);
        $service->processForm(["new_password" => 'pass', "user_id" => '']);
    }

    /**
     * @test
     **/
    public function it_not_update_user_if_password_not_given()
    {
        $mock_form_profile_success = m::mock('Jacopo\Library\Form\FormModel')->shouldReceive('process')->andReturn(true)->getMock();
        // mock user repository
        App::instance('user_repository','');
        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);
        $service->processForm(["new_password" => '', "user_id" => '']);
    }
    
    /**
     * @test
     **/
    public function it_return_user_profile_if_success()
    {
        $mock_form_profile_success = m::mock('Jacopo\Library\Form\FormModel')->shouldReceive('process')->andReturn(new UserProfile)->getMock();
        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);
        $profile = $service->processForm([]);
        $this->assertInstanceOf('Jacopo\Authentication\Models\UserProfile', $profile);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function it_not_update_profile_and_throw_exception_if_errors_perm()
    {
        $mock_auth_helper = m::mock('StdClass')->shouldReceive('checkProfileEditPermission')->once()->andReturn(false)->getMock();
        App::instance('authentication_helper', $mock_auth_helper);
        $service = new UserProfileService(new VoidValidator());
        $service->processForm(["user_id" => 1]);
    }

    /**
     * @test
     **/
    public function it_check_for_permission_and_set_error_incase()
    {
        $mock_auth_helper = m::mock('StdClass')->shouldReceive('checkProfileEditPermission')->once()->andReturn(false)->getMock();
        App::instance('authentication_helper', $mock_auth_helper);

        $service = new UserProfileService(new VoidValidator());
        try
        {
            $service->processForm(["user_id" => 1]);
        }
        catch(\Jacopo\Authentication\Exceptions\PermissionException $e)
        {}

        $errors = $service->getErrors();
        $this->assertTrue($errors->has('model'));
    }

}

class VoidValidator extends AbstractValidator
{}

class UserProfileServiceNoPermStub extends UserProfileService
{
    public function checkPermission($input = null)
    {
        //silence is golden
    }
}