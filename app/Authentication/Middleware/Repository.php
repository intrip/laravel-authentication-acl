<?php  namespace Jacopo\Authentication\Middleware;

use Jacopo\Authentication\Middleware\Models\Config as ConfigModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Jacopo\Authentication\Middleware\Interfaces\ConfigRepositoryInterface;

class Repository implements ConfigRepositoryInterface {
    protected $model;

    function __construct($model = null)
    {
        $this->model = $model ?: new ConfigModel();
    }

    public function setOption($key, $value)
    {
        try
        {
            $model = $this->model->whereKey($key)->firstOrFail();
        } catch(ModelNotFoundException $e)
        {
            $model = new ConfigModel();
        }

        $model->fill([
                             "key"   => $key,
                             "value" => $value
                     ])
              ->save();

        return $this;
    }

    public function getOption($key)
    {
        try
        {
            $model = $this->model->where('key', '=', $key)->firstOrFail();
        } catch(ModelNotFoundException $e)
        {
            return null;
        }

        return $model->value;
    }
} 