<?php

namespace app\rest\router;

use app\rest\Rest;


class Router
{
    private $app;

    private $routes = [];
    private $url;

    public function __construct(Rest $app)
    {
        $this->app = $app;
        $this->url = substr($this->app->request->url, strlen($this->app->config['common']['base_path']));
    }

    public function map($method, $route, $target)
    {
        $this->routes[] = [ $method, $route, $target ];
    }

    public function match()
    {
        foreach($this->routes as $handler) {
            list($method, $route, $target) = $handler;

            if ($method === $this->app->request->method) {
                $regex = preg_replace('`{[0-9a-zA-Z-_]+}`', '([0-9]+)', $route);

                if (preg_match("`$regex$`u", $this->url, $params)) {
                    unset($params[0]);
                    return array(
                        'target' => $target,
                        'params' => $params
                    );
                }
            }
        }
        return false;
    }

    public function run()
    {
        if ($match = $this->match()) {
            if (is_callable($match['target'])) {
                call_user_func_array($match['target'], $match['params']);
                $this->app->response->with_status(200);
            } else {
                $this->app->response->with_status(500);
            }
        } else {
            $this->app->response->with_status(404);
        }
    }
}