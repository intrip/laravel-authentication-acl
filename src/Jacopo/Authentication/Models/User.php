<?php namespace Jacopo\Authentication\Models;
/**
 * Class User
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Cartalyst\Sentry\Users\Eloquent\User as CartaUser;
use Jacopo\Library\Traits\OverrideConnectionTrait;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\LoginRequiredException;

class User extends CartaUser
{
    use OverrideConnectionTrait;

    protected $fillable = ["email", "password", "permissions", "activated", "activation_code", "activated_at", "last_login", "protected", "banned"];

    protected $guarded = ["id"];

    /**
     * Validates the user and throws
     * Exception if fails.
     *
     * @override
     * @return bool
     * @throws \Cartalyst\Sentry\Users\UserExistsException
     */
    public function validate()
    {
        if ( ! $login = $this->{static::$loginAttribute})
        {
            throw new LoginRequiredException("A login is required for a user, none given.");
        }

        // Check if the user already exists
        $query = $this->newQuery();
        $persistedUser = $query->where($this->getLoginName(), '=', $login)->first();

        if ($persistedUser and $persistedUser->getId() != $this->getId())
        {
            throw new UserExistsException("A user already exists with login [$login], logins must be unique for users.");
        }

        return true;
    }

    public function user_profile()
    {
        return $this->hasMany('Jacopo\Authentication\Models\UserProfile');
    }
} 