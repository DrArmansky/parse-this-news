<?php

namespace ParseThisNewsApi\Controller;


use ParseThisNewsApi\Exception\ValidationException;
use ParseThisNewsApi\Formatter\iFormatter;
use ParseThisNewsApi\Request\iRequest;
use ParseThisNewsApi\Response\iResponse;
use ParseThisNewsApi\Validator\iValidator;

abstract class BaseController implements iController
{
    protected iRequest $request;
    protected iResponse $response;

    public function __construct(iRequest $request, iResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function sendResponse(int $statusCode, $response = []): void
    {
        $this->response
            ->setStatusCode($statusCode)
            ->setContent($response)
            ->send();
    }

    public function sendError(\Throwable $exception): void
    {
        $this->response
            ->setStatusCode($exception->getCode())
            ->setContent(['error' => $exception->getMessage()])
            ->send();
    }

    protected function formatResponseData($response, iFormatter $formatter)
    {
        return $formatter->format($response);
    }

    protected function validateRequest(iValidator $validator): void
    {
        try {
            $validator->validate($this->request);
        } catch (ValidationException $exception) {
            $this->sendError($exception);
        }
    }
}
