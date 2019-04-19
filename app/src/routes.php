<?php

$app->router->map('POST', '/login', function () use ($app)
{
    list($email, $password) =
        $app->request->get(
            'param:email {trim,email,required}',
            'param:password {required}'
        );

    if ($api_key = $app->db->execute('login', $email, $password))
        $app->response->with_json([ 'api_key' => $api_key ]);

    $app->response->with_status(401);
});
