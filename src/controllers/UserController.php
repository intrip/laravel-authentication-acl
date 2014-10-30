<?php  namespace Jacopo\Authentication\Controllers;

/**
 * Class UserController
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\PermissionException;
use Jacopo\Authentication\Exceptions\ProfileNotFoundException;
use Jacopo\Authentication\Helpers\DbHelper;
use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Presenters\UserPresenter;
use Jacopo\Authentication\Services\UserProfileService;
use Jacopo\Authentication\Validators\UserProfileAvatarValidator;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Form\FormModel;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Helpers\FormHelper;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Validators\UserValidator;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Authentication\Validators\UserProfileValidator;
use View, Input, Redirect, App, Config, Controller;
use Jacopo\Authentication\Interfaces\AuthenticateInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * @var \Jacopo\Authentication\Repository\SentryUserRepository
     */
    protected $user_repository;
    protected $user_validator;
    /**
     * @var \Jacopo\Authentication\Helpers\FormHelper
     */
    protected $form_helper;
    protected $profile_repository;
    protected $profile_validator;
    /**
     * @var use Jacopo\Authentication\Interfaces\AuthenticateInterface;
     */
    protected $auth;
    protected $register_service;
    protected $custom_profile_repository;

    public function __construct(UserValidator $v, FormHelper $fh, UserProfileValidator $vp, AuthenticateInterface $auth)
    {
        $this->user_repository = App::make('user_repository');
        $this->user_validator = $v;
        $this->f = App::make('form_model', [$this->user_validator, $this->user_repository]);
        $this->form_helper = $fh;
        $this->profile_validator = $vp;
        $this->profile_repository = App::make('profile_repository');
        $this->auth = $auth;
        $this->register_service = App::make('register_service');
        $this->custom_profile_repository = App::make('custom_profile_repository');
    }

    public function getList()
    {
        $users = $this->user_repository->all(Input::except(['page']));

        return View::make('laravel-authentication-acl::admin.user.list')->with(["users" => $users]);
    }

    public function editUser()
    {
        try
        {
            $user = $this->user_repository->find(Input::get('id'));
        } catch(JacopoExceptionsInterface $e)
        {
            $user = new User;
        }
        $presenter = new UserPresenter($user);

        return View::make('laravel-authentication-acl::admin.user.edit')->with(["user" => $user, "presenter" => $presenter]);
    }

    public function postEditUser()
    {
        $id = Input::get('id');

		$new_user = Input::all();
		if(!$id && empty($new_user['password']))
		{
			$new_user['password'] = $new_user['password_confirmation'] = substr(str_shuffle(MD5(microtime())), 0, 10);
			$new_user['activated'] = '1'; // Forcing activation of new accounts or the password create process will fail.
		}

        DbHelper::startTransaction();
        try
        {
            $user = $this->f->process($new_user);
            $this->profile_repository->attachEmptyProfile($user);
        } catch(JacopoExceptionsInterface $e)
        {
            DbHelper::rollback();
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("users.edit", $id ? ["id" => $id] : [])->withInput()->withErrors($errors);
        }

        DbHelper::commit();
		$flash_message = "User edited with success";
		if(!$id && $new_user['send_welcome_email']) // Send welcome email to new users created by admins
		{
	        $this->sendWelcomeMail($new_user);
			$flash_message = "New user was added successfully.";
		}

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user->id])
                       ->withMessage($flash_message);
    }

	private function sendWelcomeMail($new_user)
	{

		$admin_user = $this->auth->getLoggedUser();
		$admin_name = trim($admin_user->first_name." ".$admin_user->last_name);
		if($admin_name == null)
		{
			$admin_name = $admin_user->email;
		}

        $view_file = "laravel-authentication-acl::admin.mail.welcome-new-user";
        $mailer = App::make('jmailer');
        // send email to client
        $mailer->sendTo($new_user['email'], [
			"email"      => $new_user["email"],
			"admin_name" => $admin_name,
			"token"      => App::make('authenticator')->getToken($new_user["email"])
			],
			"Invitation to join " . Config::get('laravel-authentication-acl::app_name'),
			$view_file);
	}

    public function deleteUser()
    {
        try
        {
            $this->f->delete(Input::all());
        } catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')->withErrors($errors);
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')->withMessage("User deleted with success.");
    }

    public function addGroup()
    {
        $user_id = Input::get('id');
        $group_id = Input::get('group_id');

        try
        {
            $this->user_repository->addGroup($user_id, $group_id);
        } catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])
                           ->withErrors(new MessageBag(["name" => "Group nt present."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])
                       ->withMessage("Group added with success.");
    }

    public function deleteGroup()
    {
        $user_id = Input::get('id');
        $group_id = Input::get('group_id');

        try
        {
            $this->user_repository->removeGroup($user_id, $group_id);
        } catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])
                           ->withErrors(new MessageBag(["name" => "Group not present."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])
                       ->withMessage("Group deleted with success.");
    }

    public function editPermission()
    {
        // prepare input
        $input = Input::all();
        $operation = Input::get('operation');
        $this->form_helper->prepareSentryPermissionInput($input, $operation);
        $id = Input::get('id');

        try
        {
            $obj = $this->user_repository->update($id, $input);
        } catch(JacopoExceptionsInterface $e)
        {
            return Redirect::route("users.edit")->withInput()->withErrors(new MessageBag(["permissions" => "Permission not found"]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $obj->id])
                       ->withMessage("Permission edited with success.");
    }

    public function editProfile()
    {
        $user_id = Input::get('user_id');

        try
        {
            $user_profile = $this->profile_repository->getFromUserId($user_id);
        } catch(UserNotFoundException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')
                           ->withErrors(new MessageBag(['model' => 'User not found.']));
        } catch(ProfileNotFoundException $e)
        {
            $user_profile = new UserProfile(["user_id" => $user_id]);
        }
        $custom_profile_repo = App::make('custom_profile_repository', $user_profile->id);

        return View::make('laravel-authentication-acl::admin.user.profile')->with([
                                                                                          'user_profile'   => $user_profile,
                                                                                          "custom_profile" => $custom_profile_repo
                                                                                  ]);
    }

    public function postEditProfile()
    {
        $input = Input::all();
        $service = new UserProfileService($this->profile_validator);

        try
        {
            $service->processForm($input);
        } catch(JacopoExceptionsInterface $e)
        {
            $errors = $service->getErrors();
            return Redirect::back()
                           ->withInput()
                           ->withErrors($errors);
        }
        return Redirect::back()
                       ->withInput()
                       ->withMessage("Profile edited with success.");
    }

    public function editOwnProfile()
    {
        $logged_user = $this->auth->getLoggedUser();

        $custom_profile_repo = App::make('custom_profile_repository', $logged_user->user_profile()->first()->id);

        return View::make('laravel-authentication-acl::admin.user.self-profile')->with([
                                                                                               "user_profile"   => $logged_user->user_profile()
                                                                                                                               ->first(),
                                                                                               "custom_profile" => $custom_profile_repo
                                                                                       ]);
    }

    public function signup()
    {
        $enable_captcha = Config::get('laravel-authentication-acl::captcha_signup');
        if($enable_captcha)
        {
            $captcha = App::make('captcha_validator');
            return View::make('laravel-authentication-acl::client.auth.signup')->with('captcha', $captcha);
        }

        return View::make('laravel-authentication-acl::client.auth.signup');
    }

    public function postSignup()
    {
        $service = App::make('register_service');

        try
        {
            $service->register(Input::all());
        } catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@signup')->withErrors($service->getErrors())->withInput();
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@signupSuccess');
    }

    public function signupSuccess()
    {
        $email_confirmation_enabled = Config::get('laravel-authentication-acl::email_confirmation');
        return $email_confirmation_enabled ? View::make('laravel-authentication-acl::client.auth.signup-email-confirmation') : View::make('laravel-authentication-acl::client.auth.signup-success');
    }

    public function emailConfirmation()
    {
        $email = Input::get('email');
        $token = Input::get('token');

        try
        {
            $this->register_service->checkUserActivationCode($email, $token);
        } catch(JacopoExceptionsInterface $e)
        {
            return View::make('laravel-authentication-acl::client.auth.email-confirmation')->withErrors($this->register_service->getErrors());
        }
        return View::make('laravel-authentication-acl::client.auth.email-confirmation');
    }

    public function addCustomFieldType()
    {
        $description = Input::get('description');
        $user_id = Input::get('user_id');

        try
        {
            $this->custom_profile_repository->addNewType($description);
        } catch(PermissionException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id])
                       ->with('message', "Field {$description} added succesfully.");
    }

    public function deleteCustomFieldType()
    {
        $id = Input::get('id');
        $user_id = Input::get('user_id');

        try
        {
            $this->custom_profile_repository->deleteType($id);
        } catch(ModelNotFoundException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => "Cannot find the custom field."]));
        } catch(PermissionException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@postEditProfile', ["user_id" => $user_id])
                       ->with('message', "Field removed succesfully.");
    }

    public function changeAvatar()
    {
        $user_id = Input::get('user_id');
        $profile_id = Input::get('user_profile_id');

        // validate input
        $validator = new UserProfileAvatarValidator();
        if(!$validator->validate(Input::all()))
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editProfile', ['user_id' => $user_id])
                           ->withInput()->withErrors($validator->getErrors());
        }

        // change picture
        try
        {
            $this->profile_repository->updateAvatar($profile_id);
        } catch(NotFoundException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editProfile', ['user_id' => $user_id])->withInput()
                           ->withErrors(new MessageBag(['avatar' => 'Cannot upload the file.']));
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editProfile', ['user_id' => $user_id])
                       ->withMessage('Avatar changed succesfully');
    }

    public function refreshCaptcha()
    {
        return View::make('laravel-authentication-acl::client.auth.captcha-image')
                ->with(['captcha' => App::make('captcha_validator')]);
    }
} 
