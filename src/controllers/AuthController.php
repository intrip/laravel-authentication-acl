<?php namespace Jacopo\Authentication\Controllers;

use Controller;
use View;
use Sentry;
use Input;
use Redirect;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
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

    public function getClientLogin()
    {
        return View::make('authentication::client.auth.login');
    }

    public function getAdminLogin()
    {
        return View::make('authentication::admin.auth.login');
    }

    public function postAdminLogin()
    {
        list($email, $password, $remember) = $this->getLoginInput();

        try
        {
            $this->authenticator->authenticate(array(
                                                "email" => $email,
                                                "password" => $password
                                             ), $remember);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->authenticator->getErrors();
            return Redirect::action('Jacopo\Authentication\Controllers\AuthController@getAdminLogin')->withInput()->withErrors($errors);
        }

        return Redirect::to('/admin/users/list');
    }

    public function postClientLogin()
    {
        list($email, $password, $remember) = $this->getLoginInput();

        try
        {
            $this->authenticator->authenticate(array(
                                                    "email" => $email,
                                                    "password" => $password
                                               ), $remember);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->authenticator->getErrors();
            return Redirect::action('Jacopo\Authentication\Controllers\AuthController@getClientLogin')->withInput()->withErrors($errors);
        }

        return Redirect::to('/');
    }

    /**
     * Logout utente
     * 
     * @return string
     */
    public function getLogout()
    {
        $this->authenticator->logout();

        return Redirect::to('/');
    }

    /**
     * Recupero password
     */
    public function getReminder()
    {
        return View::make("authentication::client.auth.reminder");
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
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getReminder")->withErrors($errors);
        }
    }

    public function getChangePassword()
    {
        $email = Input::get('email');
        $token = Input::get('token');

        return View::make("authentication::client.auth.changepassword", array("email" => $email, "token" => $token) );
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
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword")->withErrors($errors);
        }

    }

    /**
     * @return array
     */
    private function getLoginInput()
    {
        $email    = Input::get('email');
        $password = Input::get('password');
        $remember = Input::get('remember');

        return array($email, $password, $remember);
    }
}
