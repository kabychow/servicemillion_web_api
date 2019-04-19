<?php

namespace app\rest\http;

use app\rest\Rest;


class Request
{
    private $app;

    public $url = '/';
    public $method = 'GET';
    public $params = [];
    public $files = [];

    public function __construct(Rest $app)
    {
        $this->app = $app;

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->method = $_SERVER['REQUEST_METHOD'];
        }

        switch ($this->method) {
            case 'POST':
                $this->params = $_POST;
                break;
            case 'PUT':
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $this->params);
                break;
            default:
                $this->params = $_GET;
        }

        foreach($_FILES as $name => $file_array) {
            if (is_array($file_array['name'])) {
                foreach ($file_array as $attr => $list) {
                    foreach ($list as $index => $value) {
                        $this->files[$name][$index][$attr] = $value;
                    }
                }
            } else {
                $this->files[$name][] = $file_array;
            }
        }
    }

    public function get(...$keys)
    {
        $tmp = [];
        foreach ($keys as $key) {

            if (!preg_match('`([a-z]+)+:([a-z_]+) {([a-z_]?.*)}$`u', $key, $matches))
                $this->app->response->with_status(500);

            $type = $matches[1];
            $name = $matches[2];
            $validations = explode(',', $matches[3]);

            if ($type === 'param') $request = $this->params;
            elseif ($type === 'file') $request = $this->files;
            else $this->app->response->with_status(500);

            if (!isset($request[$name])) $this->app->response->with_status(400);
            $value = $request[$name];

            foreach ($validations as $validation) {
                $this->app->validator->validate($value, $validation);
            }

            $tmp[] = $value;
        }

        return $tmp;
    }

}