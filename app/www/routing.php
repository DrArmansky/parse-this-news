<?php

use Bramus\Router\Router;
use ParseThisNews\Controller\NewsController;
use ParseThisNews\Controller\SettingsFormController;

$router = new Router();
$settingsFormController = new SettingsFormController();

$router->get('/', function () use ($settingsFormController) {
    $settingsFormController->settingsFromAction();
});

$newsController = new NewsController();

$router->get('/news-list/', function () use ($newsController) {
    $newsController->newsListAction();
});

$router->get('/detail/([a-z0-9_-]+)', function () use ($newsController) {
    $newsController->newsDetailAction();
});

$router->run();
