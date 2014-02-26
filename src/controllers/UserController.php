<?php  namespace Jacopo\Authentication\Controllers;
/**
 * Class UserController
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use Jacopo\Library\Form\FormModel;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Validators\UserValidator;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use View, Input, Redirect, App;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends \BaseController
{
    /**
     * @var \Jacopo\Authentication\Repository\SentryUserRepository
     */
    protected $r;
    /**
     * @var \Jacopo\Authentication\Validators\UserValidator
     */
    protected $v;

    public function __construct(UserValidator $v)
    {
        $this->r = App::make('user_repository');
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
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

        return View::make('authentication::user.edit')->with(["user" => $user]);
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
} 