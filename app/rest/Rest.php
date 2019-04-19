<?php

namespace app\rest;

use app\rest\http\Request;
use app\rest\http\Response;
use app\rest\router\Router;
use app\rest\helper\Database;
use app\rest\helper\Validator;


class Rest
{
    public $config;
    public $request, $response;
    public $router;
    public $db;
    public $validator;

	public function __construct()
    {
        $this->config = parse_ini_file('app/config.ini', true);
        $this->request = new Request($this);
        $this->response = new Response();
        $this->router = new Router($this);
        $this->db = new Database($this);
        $this->validator = new Validator($this);
	}
}
