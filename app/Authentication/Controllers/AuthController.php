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
        return view('laravel-authentication-acl::client.auth.login');
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
            return redirect()->route("user.admin.login")->withInput()->withErrors($errors);
        }

        return redirect()->route('dashboard.default');
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
            return redirect()->route("user.login")->withInput()->withErrors($errors);
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

        return redirect('/');
    }

    /**
     * Recupero password
     */
    public function getReminder()
    {
        return view("laravel-authentication-acl::client.auth.reminder");
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
            return redirect()->route("user.reminder-success");
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return redirect()->route("user.recovery-password")->withErrors($errors);
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
          return redirect()->route("user.change-password")->withErrors($this->reminder_validator->getErrors())->withInput();
        }

        try
        {
            $this->reminder->reset($email, $token, $password);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->reminder->getErrors();
            return redirect()->route("user.change-password")->withErrors($errors);
        }

        return redirect()->route("user.change-password-success");

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
