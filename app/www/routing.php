<?php

use Bramus\Router\Router;
use ParseThisNews\Controller\NewsController;

$router = new Router();
$newsController = new NewsController();

$router->get('/', function () use ($newsController) {
    $newsController->newsListAction();
});

$router->get('/detail/([a-z0-9_-]+)', function () use ($newsController) {
    $newsController->newsDetailAction();
});

$router->run();
