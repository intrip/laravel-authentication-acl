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
use View, Input, Redirect, App;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends \Controller
{
    /**
     * @var \Jacopo\Authentication\Repository\SentryUserRepository
     */
    protected $r;
    /**
     * @var \Jacopo\Authentication\Validators\UserValidator
     */
    protected $v;
    /**
     * @var \Jacopo\Authentication\Helpers\FormHelper
     */
    protected $fh;
    /**
     * Profile repository
     * @var \Jacopo\Authentication\Repository\Interfaces\UserProfileRepositoryInterface
     */
    protected $r_p;
    /**
     * @var UserProfileValidator
     */
    protected $v_p;
    public function __construct(UserValidator $v, FormHelper $fh, UserProfileValidator $vp)
    {
        $this->r = App::make('user_repository');
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
        $this->fh = $fh;
        $this->v_p = $vp;
        $this->r_p = App::make('profile_repository');

    }

    public function getList()
    {
        $users = $this->r->all();

        return View::make('authentication::user.list')->with(["users" => $users]);
    }

    public function editUser()
    {
        try
        {
            $user = $this->r->find(Input::get('id'));
        }
        catch(JacopoExceptionsInterface $e)
        {
            $user = new User;
        }
        $presenter = new UserPresenter($user);

        return View::make('authentication::user.edit')->with(["user" => $user, "presenter" => $presenter]);
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

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $obj->id])->withMessage("Utente modificato con successo.");
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
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')->withMessage("Utente cancellato con successo.");
    }

    public function addGroup()
    {
        $user_id = Input::get('id');
        $group_id = Input::get('group_id');

        try
        {
            $this->r->addGroup($user_id, $group_id);
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])->withErrors(new MessageBag(["name" => "Gruppo non presente."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $user_id])->withMessage("Gruppo aggiunto con successo.");
    }

    public function deleteGroup()
    {
        $user_id = Input::get('id');
        $group_id = Input::get('group_id');

        try
        {
            $this->r->removeGroup($user_id, $group_id);
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser', ["id" => $user_id])->withErrors(new MessageBag(["name" => "Gruppo non presente."]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $user_id])->withMessage("Gruppo cancellato con successo.");
    }

    public function editPermission()
    {
        // prepare input
        $input = Input::all();
        $operation = Input::get('operation');
        $this->fh->prepareSentryPermissionInput($input, $operation);
        $id = Input::get('id');

        try
        {
            $obj = $this->r->update($id, $input);
        }
        catch(JacopoExceptionsInterface $e)
        {
            return Redirect::route("users.edit")->withInput()->withErrors(new MessageBag(["permissions" => "Permesso non trovato"]));
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editUser',["id" => $obj->id])->withMessage("Permesso modificato con successo.");
    }

    public function editProfile()
    {
        $user_id = Input::get('user_id');

        try
        {
            $user_profile = $this->r_p->getFromUserId($user_id);
        }
        catch(UserNotFoundException $e)
        {
            return Redirect::action('Jacopo\Authentication\Controllers\UserController@getList')->withErrors(new MessageBag(['model' => 'Utente non presente.']));
        }
        catch(ProfileNotFoundException $e)
        {
            $user_profile = new UserProfile(["user_id" => $user_id]);
        }

        return View::make('authentication::user.profile')->with(['user_profile' => $user_profile]);
    }

    public function postEditProfile()
    {
        $input = Input::all();
        $service = new UserProfileService($this->v_p);

        try
        {
            $user_profile = $service->processForm($input);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $service->getErrors();
            return Redirect::route("users.profile.edit", ["user_id" => $input['user_id'] ])->withInput()->withErrors($errors);
        }
        return Redirect::action('Jacopo\Authentication\Controllers\UserController@editProfile',["user_id" => $user_profile->user_id])->withMessage("Profilo modificato con successo.");
    }

    public function signup()
    {
        return View::make('authentication::user.signup');
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

        return Redirect::action('Jacopo\Authentication\Controllers\UserController@signup')->withMessage('Registration request sent successfully. Please check your email.');
    }
} 