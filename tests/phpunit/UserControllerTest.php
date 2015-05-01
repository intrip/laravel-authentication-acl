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
    use Helper;
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

        $this->route('POST', "user.signup.process");

        $this->assertRedirectedToRoute("user.signup-success");

    }

    /**
     * @test
     **/
    public function it_run_signup_and_return_errors_on_post_signup()
    {
        $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->andThrow(new ValidationException())->shouldReceive('getErrors')->once()->getMock();
        App::instance('register_service', $mock_register);

        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@postSignup');

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@signup');
        $this->assertSessionHasErrors();
    }

    /**
     * @test
     **/
    public function it_show_the_signup_view_on_signup()
    {
        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@signup');

        $this->assertResponseOk();
    }

    /**
     * @test
     **/
    public function itShowCaptchaOnSignupIfEnabled()
    {
        $this->enableCaptchaCheck();
        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@signup');

        $this->assertViewHas("captcha");
    }

    /**
     * @test
     **/
    public function it_doesnt_show_captcha_on_signup_if_disabled()
    {
        $this->disableCaptchaCheck();
        $response = $this->route('GET', "user.signup");

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

        $response = $this->route('GET', 'user.signup-success');

        $this->assertResponseIncludes($response, 'You account has been created. However');
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

        $response = $this->route('GET', 'user.signup-success');

        $this->assertResponseIncludes($response, 'Now you can login to the website ');
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

        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@emailConfirmation',
            '', [
                "email" => $email,
                "token" => $token
            ]);

        $this->assertResponseOk();
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

        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@emailConfirmation',
            '', [
                "email" => $email,
                "token" => $token
            ]);

        $this->assertResponseOk();
        $this->assertViewHas('errors');
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

        $this->route('GET', "user.email-confirmation",
            '', [
                "email" => $email,
                "token" => $token
            ]);

        $this->assertResponseOk();
        $this->assertViewHas('errors');
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

        $this->route('GET', 'users.list', [
            "new" => "new input",
            "intersect" => "new intersect"
        ]);

        $this->assertResponseOk();
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

        $this->route('POST', 'users.edit', $input_data);

        $user_created = User::get()->last();
        $this->assertNotNull($user_created);
        $profile_created = UserProfile::get()->last();
        $this->assertNotNull($profile_created);

        $this->assertRedirectedToRoute('users.edit',
            ['id' => $user_created->id]);
        $this->assertSessionHas('message');
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

        $this->route('POST', 'users.edit', $input_data);

        $user_updated = User::find($this->current_user->id);
        $this->assertEquals($new_email, $user_updated->email);

        $this->assertRedirectedToRoute('users.edit',
                                        ['id' => $user_updated->id]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function canAddCustomFieldType()
    {
        $this->loginAnAdmin();

        $field_description = "field desc";
        $this->route('POST', 'users.profile.addfield', ['description' => $field_description, 'user_id' => $this->current_user->id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        // check that have created a field type
        $this->assertCount(1, $profile_fields);

        $this->assertRedirectedToRoute('users.profile.edit', ["user_id" => $this->current_user->id]);
        $this->assertSessionHas('message');
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

        $this->route('POST', 'users.profile.addfield', ['description' => "field desc", 'user_id' => $fake_user->id]);

        $this->assertRedirectedToRoute('users.profile.edit', ["user_id" => $fake_user->id]);
        $this->assertSessionHas('errors');
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

        $response = $this->route('POST', 'users.edit.permission', $input);

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

        $this->route('POST', 'users.edit.permission', $input);

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

        $this->route('POST', 'users.profile.deletefield', ["id" => $field_id, "user_id" => $user_id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        $this->assertCount(0, $profile_fields);

        $this->assertRedirectedToRoute('users.profile.edit', ["user_id" => $user_id]);
        $this->assertSessionHas('message');
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
        $this->route('POST', 'users.profile.deletefield', ["id" => $field_id, "user_id" => $fake_user->id]);

        $this->assertRedirectedToRoute('users.profile.edit', ["user_id" => $fake_user->id]);
        $this->assertSessionHas('errors');
    }
    
    /**
     * @test
     **/
    public function can_see_profile_edit_of_himself()
    {
        $created_user = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub())->first();
        $created_user_profile = $this->make('LaravelAcl\Authentication\Models\UserProfile', $this->getUserProfileStub($created_user))->first();
        $this->loginUser($created_user);

        $response = $this->route('GET','users.selfprofile.edit');

        $this->assertResponseOk();
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
 