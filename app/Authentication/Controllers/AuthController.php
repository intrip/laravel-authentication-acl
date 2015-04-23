<?php namespace LaravelAcl\Authentication\Controllers;

use View, Sentry, Input, Redirect, App, Config;
use LaravelAcl\Authentication\Validators\ReminderValidator;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;
use LaravelAcl\Authentication\Services\ReminderService;

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
        return view('laravel-authentication-acl::admin.auth.login');
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
            return Redirect::route("user.admin.login")->withInput()->withErrors($errors);
        }

        return Redirect::route('dashboard.default');
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
            return Redirect::route("user.login")->withInput()->withErrors($errors);
        }

        return Redirect::to(Config::get('acl_base.user_login_redirect_url'));
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
            return Redirect::route("user.reminder-success");
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::route("user.recovery-password")->withErrors($errors);
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
          return Redirect::route("user.change-password")->withErrors($this->reminder_validator->getErrors())->withInput();
        }

        try
        {
            $this->reminder->reset($email, $token, $password);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return Redirect::route("user.change-password")->withErrors($errors);
        }

        return Redirect::route("user.change-password-success");

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
