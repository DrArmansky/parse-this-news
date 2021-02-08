<?php


namespace ParseThisNewsApi\Request;


use ParseThisNewsApi\Validator\iValidator;

class BaseRequest implements iRequest
{
    public function get(string $paramName)
    {
        return $this->getAll()[$paramName];
    }

    public function getAll(): array
    {
        return $_REQUEST;
    }
}