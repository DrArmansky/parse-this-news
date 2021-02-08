<?php


namespace ParseThisNewsApi\Request;


use ParseThisNewsApi\Validator\iValidator;

class BaseRequest implements iRequest
{
    protected iValidator $validator;

    public function __construct(iValidator $validator)
    {
        $this->validator = $validator;
    }

    public function get(string $paramName)
    {
        return $this->getAll()[$paramName];
    }

    public function getAll(): array
    {
        return $_REQUEST;
    }

    public function validate(): void
    {
        $this->validator->validate($this->getAll());
    }
}