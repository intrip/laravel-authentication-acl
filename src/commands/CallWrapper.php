<?php

class CallWrapper {

    protected $wrapper_obj;

    function __construct($wrapped_obj)
    {
        $this->wrapped_obj = $wrapped_obj;
    }

    public function __call($name, $params)
    {
        return call_user_func_array([$this->wrapped_obj, $name], $params);
    }

}
