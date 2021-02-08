<?php

namespace ParseThisNewsApi\Controller;


interface iController
{
    public function sendResponse(array $response, int $statusCode);

    public function sendError(\Throwable $exception);
}
