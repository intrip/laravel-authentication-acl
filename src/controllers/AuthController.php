<?php namespace Jacopo\Authentication\Controllers;

use Controller;
use View;
use Sentry;
use Input;
use Redirect;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface as Pbi;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Jacopo\Authentication\Services\ReminderService;

class AuthController extends Controller {

    protected $authenticator;
    protected $reminder;

    public function __construct(SentryAuthenticator $auth, ReminderService $reminder)
    {
        $this->authenticator = $auth;
        $this->reminder = $reminder;
    }

    /**
     * Usato per effettuare il login utente
     *
     * @return Response
     */
    public function getLogin()
    {
        return View::make('authentication::auth.login');
    }

    public function postLogin()
    {
        $email = Input::get('email');
        $password = Input::get('password');
        $remember = Input::get('remember');

        $success = $this->authenticator->authenticate(array(
                                                "email" => $email,
                                                "password" => $password
                                             ), $remember);
        if($success)
        {
            return Redirect::to('/admin/users/list');
        }
        else
        {
            $errors = $this->authenticator->getErrors();
            return Redirect::action('Jacopo\Authentication\Controllers\AuthController@getLogin')->withInput()->withErrors($errors);
        }
    }

    /**
     * Logout utente
     * 
     * @return string
     */
    public function getLogout()
    {
        $this->authenticator->logout();

        return Redirect::to('/user/login');
    }

    /**
     * Recupero password
     */
    public function getReminder()
    {
        return View::make("authentication::auth.reminder");
    }

    /**
     * Invio token per set nuova password via mail
     *
     * @return mixed
     */
    public function postReminder()
    {
        $email = Input::get('email');

        try
        {
            $this->reminder->send($email);
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getReminder")->with(array("message"=> "Abbiamo inviato un mail per il recupero password. Per piaciere controlla la tua mail box."));
        }
        catch(Pbi $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getReminder")->withErrors($errors);
        }
    }

    public function getChangePassword()
    {
        $email = Input::get('email');
        $token = Input::get('token');

        return View::make("authentication::auth.changepassword", array("email" => $email, "token" => $token) );
    }

    public function postChangePassword()
    {
        $email = Input::get('email');
        $token = Input::get('token');
        $password = Input::get('password');

        try
        {
            $this->reminder->reset($email, $token, $password);
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword")->with(array("message"=> "Password modificata con successo!"));
        }
        catch(Pbi $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword")->withErrors($errors);
        }

    }
}
