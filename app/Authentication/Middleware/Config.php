<?php namespace Jacopo\Authentication\Middleware;

use App;
use ArrayAccess;
use Jacopo\Library\Exceptions\NotFoundException;

class Config implements ArrayAccess {

    protected $config_repository;
    protected $repository;
    protected $overridden_data;
    public static $overridden = [
            "laravel-authentication-acl::captcha_signup" => true
    ];

    public function __construct($repository = null)
    {
        $this->config_repository = App::make('config');
        $this->repository = $repository ?: new Repository;
        $this->overridden_data = [];
    }

    public function get($key, $default = null)
    {
        return $this->getOverridden($key) ?: call_user_func_array([$this->config_repository, 'get'], func_get_args());
    }

    public function __get($key)
    {
        return $this->config_repository->{$key};
    }

    public function __call($method, array $params = [])
    {
        return call_user_func_array([$this->config_repository, $method], $params);
    }

    public function override($key, $value)
    {
        $this->repository->setOption($key, $value);
        // set in the overridden keys
        static::$overridden[$key] = $value;
    }

    public function getOverridden($key)
    {
        // check for overridden keys
        if(!isset(static::$overridden[$key]))
        {
            return null;
        }

        try
        {
            return $this->repository->getOption($key);
        } catch(NotFoundException $e)
        {
            return null;
        }
    }

    public function getOverriddenData()
    {
        return $this->overridden_data;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    public function offsetExists($key)
    {
        return call_user_func_array([$this->config_repository, 'has'], [$key]);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        return call_user_func_array([$this->config_repository, 'set'], [$key, $value]);
    }

    public function offsetUnset($key)
    {
        return call_user_func_array([$this->config_repository, 'set'], [$key, null]);
    }
}