<?php

namespace ParseThisNewsApi\Controller;


use ParseThisNewsApi\Formatter\iFormatter;
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

    public function sendResponse($response, int $statusCode): void
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

    protected function formatResponse($response, iFormatter $formatter)
    {
        return $formatter->format($response);
    }

    protected function validateRequest(): void
    {
        try {
            $this->request->validate();
        } catch (\Throwable $exception) {
            $this->sendError($exception);
        }
    }
}
