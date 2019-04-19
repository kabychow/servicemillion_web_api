<?php

$app->validator->add('required', function (&$value) {
    return (strlen($value) > 0);
});

$app->validator->add('email', function (&$value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
});

$app->validator->add('trim', function (&$value) {
    $value = trim($value);
    return true;
});
