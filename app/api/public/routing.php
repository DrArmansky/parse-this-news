<?php

use Bramus\Router\Router;
use ParseThisNewsApi\Controller\ParserController;
use ParseThisNewsApi\Request\BaseRequest;
use ParseThisNewsApi\Response\JSONResponse;

$router = new Router();

$router->post(
    '/api/v1/',
    function () {
        (new ParserController(
            new BaseRequest(),
            new JSONResponse()
        ))->saveSettingsAction();
    }
);

$router->run();