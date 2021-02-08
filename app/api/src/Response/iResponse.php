<?php

namespace ParseThisNewsApi\Response;


interface iResponse
{
    public function setStatusCode(int $code);

    public function setContent($content);

    public function send();
}
