<?php

namespace ParseThisNewsApi\Controller;


interface iController
{
    public function sendResponse(int $statusCode, array $response);

    public function sendError(\Throwable $exception);
}
