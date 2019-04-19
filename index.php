<?php

spl_autoload_register(function($class) {
    include str_replace('\\', '/', $class) . '.php';
});

$app = new app\rest\Rest();
require 'app/src/database.php';
require 'app/src/validations.php';
require 'app/src/routes.php';

$app->router->run();