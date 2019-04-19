<?php

namespace app\rest\http;


class Response
{
    public function with_json($data = [], $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        exit(json_encode($data));
    }

    public function with_status($code)
    {
        http_response_code($code);
        exit();
    }
}