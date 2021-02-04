<?php

namespace ParseThisNewsApi\Controller;


interface iController
{
    public function sendResponse($result, $statusCode);

    public function sendError(\Throwable $exception);
}
