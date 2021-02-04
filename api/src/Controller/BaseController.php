<?php

namespace ParseThisNewsApi\Controller;


use ParseThisNewsApi\Request\iRequest;
use ParseThisNewsApi\Response\iResponse;

abstract class BaseController implements iController
{
    private iRequest $request;
    private iResponse $response;

    public function __construct(iRequest $request, iResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    abstract public function sendResponse($resultData, $statusCode);

    abstract public function sendError(\Throwable $exception);

    protected function validateRequest()
    {
        try {
            $this->request->validate();
        } catch (\Throwable $exception) {
            $this->sendError($exception);
        }
    }
}
