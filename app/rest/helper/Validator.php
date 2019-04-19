<?php

namespace app\rest\helper;

use app\rest\Rest;


class Validator
{
    private $app;
    private $validations = [];

    public function __construct(Rest $app)
    {
        $this->app = $app;
    }

    public function add($name, $callback)
    {
        $this->validations[$name] = $callback;
    }

    public function validate(&$value, $name)
    {
        if (!isset($this->validations[$name]) || !call_user_func_array($this->validations[$name], [ &$value ])) {
            $this->app->response->with_status(422);
        }
    }
}