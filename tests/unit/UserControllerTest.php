<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use App;
use Config;
use Illuminate\Support\Facades\Event;
use Session;
use LaravelAcl\Authentication\Models\User;
use LaravelAcl\Authentication\Models\UserProfile;
use LaravelAcl\Authentication\Tests\Unit\Traits\Helper;
use LaravelAcl\Authentication\Tests\Unit\Traits\AuthHelper;
use LaravelAcl\Authentication\Validators\UserValidator;
use LaravelAcl\Library\Exceptions\ValidationException;
use Mockery as m;

/**
 * Test UserControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserControllerTest extends DbTestCase
{
    use AuthHelper;

    protected $custom_type_repository;
    protected $faker;

    protected $add_operation = 1;
    protected $remove_operation = 0;

    public function setUp()
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
        $this->custom_type_repository = App::make('custom_profile_repository');
        $this->initializeUserHasher();
    }

    public function tearDown()
    {
        m::close();
        UserValidator::resetStatic();
    }

    /**
     * @test
     **/
    public function it_run_signup_and_return_success_on_post_signup()
    {
        $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->getMock();
        App::instance('register_service', $mock_register);

        $response = $this->post("user/signup");

        $response->assertRedirect("user/signup-success");

    }

    /**
     * @test
     **/
    public function it_run_signup_and_return_errors_on_post_signup()
    {
        $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->andThrow(new ValidationException())->shouldReceive('getErrors')->once()->getMock();
        App::instance('register_service', $mock_register);

        $response = $this->post('user/signup');

        $response->assertRedirect('user/signup');
        $response->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function it_show_the_signup_view_on_signup()
    {
        $response = $this->get('user/signup');

        $response->assertStatus(200);
    }

    /**
     * @test
     **/
    public function itShowCaptchaOnSignupIfEnabled()
    {
        $this->enableCaptchaCheck();
        $response = $this->get('user/signup');

        $response->assertViewHas("captcha");
    }

    /**
     * @test
     **/
    public function it_doesnt_show_captcha_on_signup_if_disabled()
    {
        $this->disableCaptchaCheck();
        $response = $this->get("user/signup");

        $this->assertArrayNotHasKey("captcha", $response->original->getData());
    }

    protected function disableCaptchaCheck()
    {
        Config::set('acl_base.captcha_signup', false);
    }

    protected function enableCaptchaCheck()
    {
        Config::set('acl_base.captcha_signup', true);
    }

    /**
     * @test
     **/
    public function it_show_confirmation_email_success_on_signup_if_email_confirmation_is_enabled()
    {
        $this->replaceGetEmailConfirmation(true);

        $response = $this->get('user/signup-success');
        $data = $response->getOriginalContent()->render();

        $this->assertContains('You account has been created. However', $data);
    }

    private function replaceGetEmailConfirmation($active)
    {
        Config::set('acl_base.email_confirmation', $active);
    }

    /**
     * @test
     **/
    public function it_showSuccessSignup_ifEmailConfirmationIsDisabled()
    {
        $this->replaceGetEmailConfirmation(false);

        $response = $this->get('user/signup-success');
        $data = $response->getOriginalContent()->render();

        $this->assertContains('Now you can login to the website ', $data);
    }

    /**
     * @test
     **/
    public function it_show_view_with_success_if_token_is_valid()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
            $token)->getMock();
        App::instance('register_service', $mock_service);

        $response = $this->get(route("user.email-confirmation",
            [
                "email" => $email,
                "token" => $token
            ]));

        $response->assertStatus(200);
    }

    /**
     * @test
     **/
    public function it_show_view_with_error_if_token_is_invalid()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
            $token)->andThrow(new \LaravelAcl\Authentication\Exceptions\TokenMismatchException)->shouldReceive('getErrors')->once()->andReturn("")->getMock();
        App::instance('register_service', $mock_service);

        $response = $this->get(route("user.email-confirmation",
            [
                "email" => $email,
                "token" => $token
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('errors');
    }

    /**
     * @test
     **/
    public function it_show_view_errors_if_user_is_not_found()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
            $token)->andThrow(new \LaravelAcl\Authentication\Exceptions\UserNotFoundException())->shouldReceive('getErrors')->once()->andReturn("")->getMock();
        App::instance('register_service', $mock_service);

        $response = $this->get(route("user.email-confirmation",
            [
                "email" => $email,
                "token" => $token
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('errors');
    }

    /**
     * @test
     **/
    public function it_show_user_lists_on_lists()
    {
        $this->loginAnAdmin();

        Session::put('_old_input', [
            "intersect" => "old intersect",
            "old" => "old input"
        ]);

        $response = $this->get('admin/users/list', [
            "new" => "new input",
            "intersect" => "new intersect"
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     **/
    public function createNewUserWithSuccess()
    {
        $this->loginAnAdmin();

        $input_data = [
            "id" => "",
            "email" => $this->faker->email(),
            "password" => "password",
            "form_name" => "user",
            "password_confirmation" => "password",
            "activated" => true
        ];

        $response = $this->post('admin/users/edit', $input_data);

        $user_created = User::get()->last();
        $this->assertNotNull($user_created);
        $profile_created = UserProfile::get()->last();
        $this->assertNotNull($profile_created);

        $response->assertRedirect(route('users.edit', ['id' => $user_created->id]));
        $response->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function editAnUserWithSuccess()
    {
        $this->loginAnAdmin();

        $new_email = "new@mail.com";
        $input_data = [
                "id" => $this->current_user->id,
                "form_name" => "user",
                "email" => $new_email,
                "password" => '',
                "password_confirmation" => ''
        ];

        $response = $this->post('admin/users/edit', $input_data);

        $user_updated = User::find($this->current_user->id);
        $this->assertEquals($new_email, $user_updated->email);

        $response->assertRedirect(route('users.edit', ['id' => $user_updated->id]));
        $response->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function canAddCustomFieldType()
    {
        $this->loginAnAdmin();

        $field_description = "field desc";
        $response = $this->post('admin/users/profile/addField', ['description' => $field_description, 'user_id' => $this->current_user->id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        // check that have created a field type
        $this->assertCount(1, $profile_fields);

        $response->assertRedirect(route('users.profile.edit', ["user_id" => $this->current_user->id]));
        $response->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function check_for_permissions_when_adding_custom_field_type()
    {
        // create a fake user with id=1
        $fake_user = $this->make('LaravelAcl\Authentication\Models\User')[0];
        // login another user
        $this->loginAnUser();

        $response = $this->post(route('users.profile.addfield', ['description' => "field desc", 'user_id' => $fake_user->id]));

        $response->assertRedirect(route('users.profile.edit', ["user_id" => $fake_user->id]));
        $response->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function canRemoveAPermission()
    {
        $this->loginAnAdmin();
        $permission_name = "_perm";

        // create a user with permission _perm
        $user_created = $this->make('LaravelAcl\Authentication\Models\User', array_merge($this->getUserStub(),["permissions" => [$permission_name => 1]] ) )[0];

        $input = [
                "permissions" => $permission_name,
                "id" => $user_created->id,
                "operation" => $this->remove_operation,
        ];

        $response = $this->post('admin/users/editpermission', $input);

        $user_found = User::find($user_created->id);
        $this->assertEmpty($user_found->permissions);
    }

    /**
     * @test
     **/
    public function can_add_a_permission()
    {
        $this->loginAnAdmin();
        $permission_name = "_perm";

        // create a user with permission _perm
        $user_created = $this->make('LaravelAcl\Authentication\Models\User', array_merge($this->getUserStub(),["permissions" => [$permission_name => 1]] ) )[0];

        $input = [
            "permissions" => $permission_name,
            "id" => $user_created->id,
            "operation" => $this->add_operation,
        ];

        $this->post('admin/users/editpermission', $input);

        $user_found = User::find($user_created->id);
        $this->assertUserHasPermission($user_found, $permission_name);
    }

    /**
     * @test
     **/
    public function canDeleteCustomFieldType()
    {
        $this->loginAnAdmin();
        $this->stopPermissionCheckEvent();
        $field_id = $this->createFieldType();
        $user_id = 1;

        $response = $this->post('admin/users/profile/deleteField', ["id" => $field_id, "user_id" => $user_id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        $this->assertCount(0, $profile_fields);

        $response->assertRedirect(route('users.profile.edit', ["user_id" => $user_id]));
        $response->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function check_for_permissions_when_removing_custom_field_type()
    {
        // create a fake user with id=1
        $fake_user = $this->make('LaravelAcl\Authentication\Models\User')[0];
        // login another user
        $this->loginAnUser();

        $this->stopPermissionCheckEvent();
        $field_id = 1;
        $response = $this->post('admin/users/profile/deleteField', ["id" => $field_id, "user_id" => $fake_user->id]);

        $response->assertRedirect(route('users.profile.edit', ["user_id" => $fake_user->id]));
        $response->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function can_see_profile_edit_of_himself()
    {
        $created_user = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub())->first();
        $created_user_profile = $this->make('LaravelAcl\Authentication\Models\UserProfile', $this->getUserProfileStub($created_user))->first();
        $this->loginUser($created_user);

        $response = $this->get('admin/users/profile/self');

        $response->assertStatus(200);
        $view_user_profile = $response->original->user_profile;
        $this->assertObjectHasAllAttributes($created_user_profile->toArray(), $view_user_profile);
    }

    /**
     * @return mixed
     */
    protected function stopPermissionCheckEvent()
    {
        $this->stopPermissionCheckDelete();
        $this->stopPermissionCheckCreate();
    }

    /**
     * @return mixed
     */
    protected function stopPermissionCheckDelete()
    {
        return Event::listen(['customprofile.deleting'], function () {
            return false;
        }, 100);
    }

    protected function stopPermissionCheckCreate()
    {
        Event::listen(['customprofile.creating',], function () {
            return false;
        }, 100);
    }

    /**
     * @return mixed
     */
    protected function createFieldType()
    {
        $description = "description";
        $field_id = $this->custom_type_repository->addNewType($description)->id;
        return $field_id;
    }

    /**
     * @param $user_found
     * @param $permission_name
     */
    protected function assertUserHasPermission($user_found, $permission_name)
    {
        $this->assertEquals($user_found->permissions[$permission_name], 1);
    }

    /**
     * @param $created_user
     */
    protected function isLoggedUserWithProfile($created_user)
    {
        $mock_logged_user = m::mock('LaravelAcl\Authentication\Interfaces\AuthenticateInterface')->shouldReceive('getLoggedUser')
                             ->once()
                             ->andReturn($created_user)
                             ->getMock();
        App::instance('LaravelAcl\Authentication\Interfaces\AuthenticateInterface', $mock_logged_user);
    }
}
 
