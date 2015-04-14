<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use App;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Facade;
use LaravelAcl\Authentication\Models\User;
use LaravelAcl\Authentication\Models\UserProfile;
use LaravelAcl\Authentication\Tests\Unit\Traits\UserFactory;
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
    use UserFactory;

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

        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@postSignup');

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@signupSuccess');

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
    public function itDoesntShowCaptchaOnSignupIfDisabled()
    {
        $this->disableCaptchaCheck();
        $response = $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@signup');

        $this->assertArrayNotHasKey("captcha", $response->original->getData());
    }

    protected function disableCaptchaCheck()
    {
        Config::set('laravel-authentication-acl::captcha_signup', false);
    }

    protected function enableCaptchaCheck()
    {
        Config::set('laravel-authentication-acl::captcha_signup', true);
    }

    /**
     * @test
     **/
    public function it_showConfirmationEmailSuccessOnSignup_ifEmailConfirmationIsEnabled()
    {
        $active = true;
        $this->mockConfigGetEmailConfirmation($active);

        \View::shouldReceive('make')->once()->with('laravel-authentication-acl::client.auth.signup-email-confirmation');

        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@signupSuccess');
    }

    private function mockConfigGetEmailConfirmation($active)
    {
        Config::set('laravel-authentication-acl::email_confirmation', $active);
    }

    /**
     * @test
     **/
    public function it_showSuccessSignup_ifEmailConfirmationIsDisabled()
    {
        $active = false;
        $this->mockConfigGetEmailConfirmation($active);

        \View::shouldReceive('make')->once()->with('laravel-authentication-acl::client.auth.signup-success');

        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@signupSuccess');
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
    public function it_show_user_lists_on_lists()
    {
        \Session::put('_old_input', [
            "intersect" => "old intersect",
            "old" => "old input"
        ]);

        $this->action('GET', 'LaravelAcl\Authentication\Controllers\UserController@getList', [
            "new" => "new input",
            "intersect" => "new intersect"
        ]);

        $this->assertResponseOk();
    }

    /**
     * @test
     * @group valid
     **/
    public function createNewUserWithSuccess()
    {
        $input_data = [
            "id" => "",
            "email" => $this->faker->email(),
            "password" => "password",
            "form_name" => "user",
            "password_confirmation" => "password",
            "activated" => true
        ];


        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@postEditUser', $input_data);

        $user_created = User::firstOrFail();
        $this->assertNotNull($user_created);
        $profile_created = UserProfile::firstOrFail();
        $this->assertNotNull($profile_created);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@editUser',
            ['id' => $user_created->id]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function editAnUserWithSuccess()
    {
        $user_created = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub());

        $new_email = "new@mail.com";
        $input_data = [
                "id" => $user_created[0]->id,
                "form_name" => "user",
                "email" => $new_email,
                "password" => '',
                "password_confirmation" => ''
        ];

        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@postEditUser', $input_data);

        $user_updated = User::find($user_created[0]->id);
        $this->assertEquals($new_email, $user_updated->email);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@editUser',
                                        ['id' => $user_updated->id]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function canAddCustomFieldType()
    {
        $this->stopPermissionCheckEvent();
        $field_description = "field desc";
        $user_id = 1;
        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@addCustomFieldType', ['description' => $field_description, 'user_id' => $user_id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        // check that have created a field type
        $this->assertCount(1, $profile_fields);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function itHandleCreationPermissions_OnCustomFieldType()
    {
        $field_description = "field desc";
        $user_id = 1;
        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@addCustomFieldType', ['description' => $field_description, 'user_id' => $user_id]);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id]);
        $this->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function canRemoveAPermission()
    {
        $user_created = $this->make('LaravelAcl\Authentication\Models\User', array_merge($this->getUserStub(),["permissions" => ["_perm" => 1]]));

        $permission_name = "_perm";
        $input = [
                "permissions" => $permission_name,
                "id" => $user_created[0]->id,
                "operation" => $this->remove_operation,
        ];

        $this->route('POST', 'users.edit.permission', $input);

        $user_found = User::find($user_created[0]->id);
        $this->assertEmpty($user_found->permissions);
    }

    /**
     * @test
     **/
    public function canAddAPermission()
    {
        $user_created = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub());

        $permission_name = "_perm";
        $input = [
            "permissions" => $permission_name,
            "id" => $user_created[0]->id,
            "operation" => $this->add_operation,
        ];

        $this->route('POST', 'users.edit.permission', $input);

        $user_found = User::find($user_created[0]->id);
        $this->assertUserHasPermission($user_found, $permission_name);
    }

    /**
     * @test
     **/
    public function canDeleteCustomFieldType()
    {
        $this->stopPermissionCheckEvent();
        $field_id = $this->createFieldType();
        $user_id = 1;

        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@deleteCustomFieldType', ["id" => $field_id, "user_id" => $user_id]);

        $profile_fields = $this->custom_type_repository->getAllTypes();
        $this->assertCount(0, $profile_fields);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id]);
        $this->assertSessionHas('message');
    }

    /**
     * @test
     **/
    public function itHandleDeleteErrors()
    {
        $this->stopPermissionCheckEvent();
        $user_id = 1;
        $field_id = 1;
        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@deleteCustomFieldType', ["id" => $field_id, "user_id" => $user_id]);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id]);
        $this->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function itHandleDeletePemissionError()
    {
        $this->stopPermissionCheckCreate();
        $field_id = $this->createFieldType();
        $user_id = 1;

        $this->action('POST', 'LaravelAcl\Authentication\Controllers\UserController@deleteCustomFieldType', ["id" => $field_id, "user_id" => $user_id]);

        $this->assertRedirectedToAction('LaravelAcl\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id]);
        $this->assertSessionHas('errors');
    }
    
    /**
     * @test
     **/
    public function canViewEditSelfProfile()
    {
        $created_user = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub())->first();
        $created_user_profile = $this->make('LaravelAcl\Authentication\Models\UserProfile', $this->getUserProfileStub($created_user))->first();
        $this->isLoggedUserWithProfile($created_user);

        $this->route('GET','users.selfprofile.edit');

        $this->assertResponseOk();
        $view_user_profile = $this->client->getResponse()->original->user_profile;
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
 