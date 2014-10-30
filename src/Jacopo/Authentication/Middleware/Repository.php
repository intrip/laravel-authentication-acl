<?php  namespace Jacopo\Authentication\Middleware;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Jacopo\Authentication\Middleware\Interfaces\ConfigRepositoryInterface;
use App;

class Repository implements ConfigRepositoryInterface {
    protected $session;

    protected $session_test_key = 'laravel-authentication-acl.tests.config';

    function __construct($session = null)
    {
        $this->session = $session ?: App::make('session');
    }

    public function setOption($key, $value)
    {
        $this->session->set($key,$value);

        return $this;
    }

    public function getOption($key)
    {
        return $this->session->get($key);
    }
} 