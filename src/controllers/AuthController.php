<?php namespace Jacopo\Authentication\Controllers;

use Controller, View, Sentry, Input, Redirect, App, Config;
use Jacopo\Authentication\Validators\ReminderValidator;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Authentication\Services\ReminderService;
use Jacopo\Library\Exceptions\ValidationException;

class AuthController extends Controller {

    protected $authenticator;
    protected $reminder;
    protected $reminder_validator;

    public function __construct(ReminderService $reminder, ReminderValidator $reminder_validator)
    {
        $this->authenticator = App::make('authenticator');
        $this->reminder = $reminder;
        $this->reminder_validator = $reminder_validator;
    }

    public function getClientLogin()
    {
        return View::make('laravel-authentication-acl::client.auth.login');
    }

    public function getAdminLogin()
    {
        return View::make('laravel-authentication-acl::admin.auth.login');
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

        return Redirect::to('/admin/users/dashboard');
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

        return Redirect::to(Config::get('laravel-authentication-acl::config.user_login_redirect_url'));
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
        return View::make("laravel-authentication-acl::client.auth.reminder");
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
            return Redirect::to("/user/reminder-success");
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

        return View::make("laravel-authentication-acl::client.auth.changepassword", array("email" => $email, "token" => $token) );
    }

    public function postChangePassword()
    {
        $email = Input::get('email');
        $token = Input::get('token');
        $password = Input::get('password');

        if (! $this->reminder_validator->validate(Input::all()) )
        {
          return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword")->withErrors($this->reminder_validator->getErrors())->withInput();
        }

        try
        {
            $this->reminder->reset($email, $token, $password);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword")->withErrors($errors);
        }

        return Redirect::to("user/change-password-success");

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
