<?php namespace Jacopo\Authentication\Classes;
/**
 * Class SentryAuthenticator
 *
 * Sentry authenticate implementatione
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Interfaces\AuthenticateInterface;

class SentryAuthenticator implements AuthenticateInterface{

    protected $errors;

    protected $sentry;

    public function __construct()
    {
        $this->sentry = \App::make('sentry');
        $this->errors = new MessageBag();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($credentials, $remember = false)
    {
        try
        {
            $user = $this->sentry->authenticate($credentials, $remember);
        }
        catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $this->errors->add('login','Il campo login è richiesto.');
        }
        catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $this->errors->add('login','Login fallito.');
        }
        catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $this->errors->add('login','Utente non è stato attivato.');
        }
        catch(\Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $this->errors->add('login','Il campo password è richiesto.');
        }

        return $this->errors->isEmpty() ? $user : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function loginById($id, $remember = false)
    {
        $user = $this->sentry->findUserById($id);

        try
        {
            $this->sentry->login($user, false);
        }
        catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $this->errors->add('login','Login richiesto.');
        }
        catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $this->errors->add('login','Utente non attivo.');
        }
        catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $this->errors->add('login','Utente non trovato.');
        }

        return $this->errors->isEmpty() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->sentry->logout();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($email)
    {
        try
        {
            $user = $this->sentry->findUserByLogin($email);
        }
        catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            throw new UserNotFoundException($e->getMessage());
        }
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivationToken($email)
    {
        $user = $this->getUser($email);

        return $user->getActivationCode();
    }
}
