<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Exceptions\PermissionException;
use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Services\UserProfileService;
use Jacopo\Library\Exceptions\ValidationException;
use Jacopo\Library\Validators\AbstractValidator;
use Mockery as m;
use App;
/**
 * Test UserProfileServiceTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfileServiceTest extends DbTestCase {

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
        $user_profile_stub = new \StdClass;
        $user_profile_stub->id = 1;
        $mock_form_profile_success = $this->mockProfileProcessAndReturn($user_profile_stub);
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
     * @deprecated
     **/
    public function it_update_user_password_if_given()
    {
        $mock_form_profile_success = $this->mockProfileProcessAndReturn(true);
        // mock user repository
        $mock_user_repo = m::mock('StdClass')->shouldReceive('update')->once()->andReturn(true)->getMock();
        App::instance('user_repository',$mock_user_repo);
        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);

        $service->processForm(["new_password" => 'pass', "user_id" => '']);
    }

    /**
     * @test
     **/
    public function itSaveCustomProfileFieldsIfGiven()
    {
        $first_type_id            = 1;
        $first_type_value         = "value1";
        $second_type_id            = 2;
        $second_type_value         = "value2";
        $this->mockCustomProfileRepositorySetFields($first_type_id, $first_type_value,
                                                    $second_type_id, $second_type_value);

        $user_profile_stub = new \StdClass;
        $user_profile_stub->id = 1;
        $mock_form_profile_success = $this->mockProfileProcessAndReturn($user_profile_stub);

        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);

        $service->processForm(["custom_profile_{$first_type_id}" => $first_type_value, "custom_profile_{$second_type_id}" => $second_type_value]);
    }

    /**
     * @param $first_type_id
     * @param $first_type_value
     * @param $second_type_id
     * @param $second_type_value
     */
    protected function mockCustomProfileRepositorySetFields($first_type_id, $first_type_value, $second_type_id, $second_type_value)
    {
        $mock_custom_profile_repo = m::mock('StdClass')->shouldReceive('setField')->once()->with($first_type_id,
                                                                                                 $first_type_value)->shouldReceive('setField')->once()->with($second_type_id,
                                                                                                                                                             $second_type_value)->getMock();
        App::instance('custom_profile_repository', $mock_custom_profile_repo);
    }

    /**
     * @test
     **/
    public function it_not_update_user_if_password_not_given()
    {
        $user_profile_stub = new \StdClass;
        $user_profile_stub->id = 1;
        $mock_form_profile_success = $this->mockProfileProcessAndReturn($user_profile_stub);        // mock user repository
        App::instance('user_repository','');
        $service = new UserProfileServiceNoPermStub(new VoidValidator(), $mock_form_profile_success);
        $service->processForm(["new_password" => '', "user_id" => '']);
    }

    /**
     * @test
     **/
    public function it_return_user_profile_if_success()
    {
        $mock_form_profile_success = $this->mockProfileProcessAndReturn(new UserProfile());
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

    /**
     * @return m\MockInterface
     */
    private function mockProfileProcessAndReturn($return)
    {
        $mock_form_profile_success = m::mock('Jacopo\Library\Form\FormModel')->shouldReceive('process')->andReturn($return)->getMock();

        return $mock_form_profile_success;
    }

}

class VoidValidator extends AbstractValidator
{}

class UserProfileServiceNoProfilePermStub extends UserProfileService
{
    protected function checkProfileEditPermission($input = null)
    {
        //silence is golden
    }
}

class UserProfileServiceNoPermStub extends UserProfileServiceNoProfilePermStub
{
    protected function checkCustomProfileEditPermission()
    {
        //silence is golden
    }
}
