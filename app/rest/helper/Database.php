<?php

namespace app\rest\helper;

use app\rest\Rest;
use mysqli;


class Database
{
    private $app;
    private $con;
    private $actions = [];

    public function __construct(Rest $app)
    {
        $this->app = $app;
        $db_config = $this->app->config['db'] ?? null;
        if ($db_config) {
            $this->con = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['name']);
            if ($this->con->connect_errno) $this->app->response->with_status(500);
        }
    }

    public function add($name, $callback)
    {
        $this->actions[$name] = $callback;
    }

    public function execute($name, ...$vars)
    {
        if (!$this->con) $this->app->response->with_status(500);
        $stmt = $this->con->stmt_init();
        array_unshift($vars, $stmt);
        $result = call_user_func_array($this->actions[$name], $vars);
        $error_no = $stmt->errno;
        $stmt->close();
        $this->check_error($error_no);
        return $result;
    }

    private function check_error($error_no)
    {
        switch ($error_no) {
            case 0:
                break;
            case 1265:
            case 1406:
            case 1452:
                $this->app->response->with_status(422);
                break;
            case 1062:
                $this->app->response->with_status(409);
                break;
            default:
                $this->app->response->with_status(500);
        }
    }
}