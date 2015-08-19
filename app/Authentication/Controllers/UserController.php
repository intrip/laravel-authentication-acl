<?php  namespace LaravelAcl\Authentication\Controllers;

/**
 * Class UserController
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use LaravelAcl\Authentication\Exceptions\PermissionException;
use LaravelAcl\Authentication\Exceptions\ProfileNotFoundException;
use LaravelAcl\Authentication\Helpers\DbHelper;
use LaravelAcl\Authentication\Models\UserProfile;
use LaravelAcl\Authentication\Presenters\UserPresenter;
use LaravelAcl\Authentication\Services\UserProfileService;
use LaravelAcl\Authentication\Validators\UserProfileAvatarValidator;
use LaravelAcl\Library\Exceptions\NotFoundException;
use LaravelAcl\Authentication\Models\User;
use LaravelAcl\Authentication\Helpers\FormHelper;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException;
use LaravelAcl\Authentication\Validators\UserValidator;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;
use LaravelAcl\Authentication\Validators\UserProfileValidator;
use View, Input, Redirect, App, Config;
use LaravelAcl\Authentication\Interfaces\AuthenticateInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller {
    /**
     * @var \LaravelAcl\Authentication\Repository\SentryUserRepository
     */
    protected $user_repository;
    protected $user_validator;
    /**
     * @var \LaravelAcl\Authentication\Helpers\FormHelper
     */
    protected $form_helper;
    protected $profile_repository;
    protected $profile_validator;
    /**
     * @var use LaravelAcl\Authentication\Interfaces\AuthenticateInterface;
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

        DbHelper::startTransaction();
        try
        {
            $user = $this->f->process(Input::all());
            $this->profile_repository->attachEmptyProfile($user);
        } catch(JacopoExceptionsInterface $e)
        {
            DbHelper::rollback();
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("users.edit", $id ? ["id" => $id] : [])->withInput()->withErrors($errors);
        }

        DbHelper::commit();

        return Redirect::route('users.edit', ["id" => $user->id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_edit_success'));
    }

    public function deleteUser()
    {
        try
        {
            $this->f->delete(Input::all());
        } catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::route('users.list')->withErrors($errors);
        }
        return Redirect::route('users.list')
                       ->withMessage(Config::get('acl_messages.flash.success.user_delete_success'));
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
            return Redirect::route('users.edit', ["id" => $user_id])
                           ->withErrors(new MessageBag(["name" => Config::get('acl_messages.flash.error.user_group_not_found')]));
        }
        return Redirect::route('users.edit', ["id" => $user_id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_group_add_success'));
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
            return Redirect::route('users.edit', ["id" => $user_id])
                           ->withErrors(new MessageBag(["name" => Config::get('acl_messages.flash.error.user_group_not_found')]));
        }
        return Redirect::route('users.edit', ["id" => $user_id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_group_delete_success'));
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
            return Redirect::route("users.edit")->withInput()
                           ->withErrors(new MessageBag(["permissions" => Config::get('acl_messages.flash.error.user_permission_not_found')]));
        }
        return Redirect::route('users.edit', ["id" => $obj->id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_permission_add_success'));
    }

    public function editProfile()
    {
        $user_id = Input::get('user_id');

        try
        {
            $user_profile = $this->profile_repository->getFromUserId($user_id);
        } catch(UserNotFoundException $e)
        {
            return Redirect::route('users.list')
                           ->withErrors(new MessageBag(['model' => Config::get('acl_messages.flash.error.user_user_not_found')]));
        } catch(ProfileNotFoundException $e)
        {
            $user_profile = new UserProfile(["user_id" => $user_id]);
        }
        $custom_profile_repo = App::make('custom_profile_repository', [$user_profile->id]);

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
                       ->withMessage(Config::get('acl_messages.flash.success.user_profile_edit_success'));
    }

    public function editOwnProfile()
    {
        $logged_user = $this->auth->getLoggedUser();

        $custom_profile_repo = App::make('custom_profile_repository', [$logged_user->user_profile()->first()->id]);

        return View::make('laravel-authentication-acl::admin.user.self-profile')
                   ->with([
                                  "user_profile"   => $logged_user->user_profile()
                                                                  ->first(),
                                  "custom_profile" => $custom_profile_repo
                          ]);
    }

    public function signup()
    {
        $enable_captcha = Config::get('acl_base.captcha_signup');

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
            return Redirect::route('user.signup')->withErrors($service->getErrors())->withInput();
        }

        return Redirect::route("user.signup-success");
    }

    public function signupSuccess()
    {
        $email_confirmation_enabled = Config::get('acl_base.email_confirmation');
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
            return Redirect::route('users.profile.edit', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::route('users.profile.edit', ["user_id" => $user_id])
                       ->with('message', Config::get('acl_messages.flash.success.custom_field_added'));
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
            return Redirect::route('users.profile.edit', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => Config::get('acl_messages.flash.error.custom_field_not_found')]));
        } catch(PermissionException $e)
        {
            return Redirect::route('users.profile.edit', ["user_id" => $user_id])
                           ->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::route('users.profile.edit', ["user_id" => $user_id])
                       ->with('message', Config::get('acl_messages.flash.success.custom_field_removed'));
    }

    public function changeAvatar()
    {
        $user_id = Input::get('user_id');
        $profile_id = Input::get('user_profile_id');

        // validate input
        $validator = new UserProfileAvatarValidator();
        if(!$validator->validate(Input::all()))
        {
            return Redirect::route('users.profile.edit', ['user_id' => $user_id])
                           ->withInput()->withErrors($validator->getErrors());
        }

        // change picture
        try
        {
            $this->profile_repository->updateAvatar($profile_id);
        } catch(NotFoundException $e)
        {
            return Redirect::route('users.profile.edit', ['user_id' => $user_id])->withInput()
                           ->withErrors(new MessageBag(['avatar' => Config::get('acl_messages.flash.error.')]));
        }

        return Redirect::route('users.profile.edit', ['user_id' => $user_id])
                       ->withMessage(Config::get('acl_messages.flash.success.avatar_edit_success'));
    }

    public function refreshCaptcha()
    {
        return View::make('laravel-authentication-acl::client.auth.captcha-image')
                   ->with(['captcha' => App::make('captcha_validator')]);
    }
} 