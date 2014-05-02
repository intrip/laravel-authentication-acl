<?php  namespace Jacopo\Authentication\Controllers;
/**
 * Class UserController
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\ProfileNotFoundException;
use Jacopo\Authentication\Models\UserProfile;
use Jacopo\Authentication\Presenters\UserPresenter;
use Jacopo\Authentication\Services\UserProfileService;
use Jacopo\Library\Form\FormModel;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Helpers\FormHelper;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Validators\UserValidator;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Authentication\Validators\UserProfileValidator;
use View, Input, Redirect, App, Config;
use Jacopo\Authentication\Interfaces\AuthenticateInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends \Controller
{
    /**
     * @var \Jacopo\Authentication\Repository\SentryUserRepository
     */
    protected $user_repository;
    /**
     * @var \Jacopo\Authentication\Validators\UserValidator
     */
    protected $user_validator;
    /**
     * @var \Jacopo\Authentication\Helpers\FormHelper
     */
    protected $form_helper;
    /**
     * Profile repository
     * @var \Jacopo\Authentication\Repository\Interfaces\UserProfileRepositoryInterface
     */
    protected $profile_repository;
    /**
     * @var UserProfileValidator
     */
    protected $profile_validator;
    /**
     * @var use Jacopo\Authentication\Interfaces\AuthenticateInterface;
     */
    protected $auth;
    /**
     * Register Service
     */
    protected $register_service;

    public function __construct(UserValidator $v, FormHelper $fh, UserProfileValidator $vp, AuthenticateInterface $auth)
    {
        $this->user_repository = App::make('user_repository');
        $this->user_validator = $v;
        $this->f = App::make('form_model',[$this->user_validator, $this->user_repository]);
        $this->form_helper = $fh;
        $this->profile_validator = $vp;
        $this->profile_repository = App::make('profile_repository');
        $this->auth = $auth;
        $this->register_service = App::make('register_service');

    }

  public function dashboard()
  {
    return View::make('authentication::admin.user.dashboard');
  }

  public function getList()
    {
        $users = $this->user_repository->all(Input::except(['page']));

        return View::make('authentication::admin.user.list')->with(["users" => $users]);
    }

    public function editUser()
    {
        try
        {
            $user = $this->user_repository->find(Input::get('id'));
        }
        catch(JacopoExceptionsInterface $e)
        {
            $user = new User;
        }
        $presenter = new UserPresenter($user);

        return View::make('authentication::admin.user.edit')->with(["user" => $user, "presenter" => $presenter]);
    }

    public function postEditUser()
    {
        $id = Input::get('id');

        try
        {
            $obj = $this->f->process(Input::all());
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("users.edit", $id ? ["id" => $id]: [])->withInput()->withErrors($errors);
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $obj->id])->withMessage("User edited with success.");
    }

    public function deleteUser()
    {
        try
        {
            $this->f->delete(Input::all());
        }
        catch(JacopoExceptionsInterface $e)
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
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])->withErrors(new MessageBag(["name" => "Group nt present."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $user_id])->withMessage("Group added with success.");
    }

    public function deleteGroup()
    {
        $user_id = Input::get('id');
        $group_id = Input::get('group_id');

        try
        {
            $this->user_repository->removeGroup($user_id, $group_id);
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])->withErrors(new MessageBag(["name" => "Group not present."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $user_id])->withMessage("Group deleted with success.");
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
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::route("users.edit")->withInput()->withErrors(new MessageBag(["permissions" => "Permission not found"]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $obj->id])->withMessage("Permission edited with success.");
    }

    public function editProfile()
    {
        $user_id = Input::get('user_id');

        try
        {
            $user_profile = $this->profile_repository->getFromUserId($user_id);
        }
        catch(UserNotFoundException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')->withErrors(new MessageBag(['model' => 'User not found.']));
        }
        catch(ProfileNotFoundException $e)
        {
            $user_profile = new UserProfile(["user_id" => $user_id]);
        }

        return View::make('authentication::admin.user.profile')->with(['user_profile' => $user_profile]);
    }

    public function postEditProfile()
    {
        $input = Input::all();
        $service = new UserProfileService($this->profile_validator);

        try
        {
            $user_profile = $service->processForm($input);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $service->getErrors();
            return Redirect::route("users.profile.edit", ["user_id" => $input['user_id'] ])->withInput()->withErrors($errors);
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editProfile',["user_id" => $user_profile->user_id])->withMessage("Profile edited with success.");
    }

    public function signup()
    {
        return View::make('authentication::client.auth.signup');
    }

    public function postSignup()
    {
        $service = App::make('register_service');

        try
        {
            $service->register(Input::all());
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@signup')->withErrors($service->getErrors())->withInput();
        }

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@signupSuccess');
    }

    public function signupSuccess()
    {
        $email_confirmation_enabled = Config::get('authentication::email_confirmation');
        return $email_confirmation_enabled ? View::make('authentication::client.auth.signup-email-confirmation') : View::make('authentication::client.auth.signup-success');
    }

    public function emailConfirmation()
    {
        $email = Input::get('email');
        $token = Input::get('token');

        try
        {
            $this->register_service->checkUserActivationCode($email, $token);
        }
        catch(JacopoExceptionsInterface $e)
        {
            return View::make('authentication::client.auth.email-confirmation')->withErrors($this->register_service->getErrors());
        }
        return View::make('authentication::client.auth.email-confirmation');
    }
} 