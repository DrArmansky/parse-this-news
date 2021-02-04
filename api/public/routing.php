<?php

$router = new \Bramus\Router\Router();

$router->get('/api/v1/', function () {
    echo 'api test';
});

$router->run();