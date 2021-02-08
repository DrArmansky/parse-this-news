<?php

namespace ParseThisNewsApi\Request;


interface iRequest
{
    public function get(string $paramName);

    public function getAll(): array;
}
