<?php

use Bramus\Router\Router;
use ParseThisNewsApi\Controller\ParserController;
use ParseThisNewsApi\Request\BaseRequest;
use ParseThisNewsApi\Response\JSONResponse;

$router = new Router();

$router->post(
    '/api/v1/parse/',
    function () {
        (new ParserController(
            new BaseRequest(),
            new JSONResponse()
        ))->parseAction();
    }
);

$router->post(
    '/api/v1/settings/',
    function () {
        (new ParserController(
            new BaseRequest(),
            new JSONResponse()
        ))->saveSettingsAction();
    }
);

$router->get(
    '/api/v1/sources/',
    function () {
        (new ParserController(
            new BaseRequest(),
            new JSONResponse()
        ))->getSourceList();
    }
);

$router->run();