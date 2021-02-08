<?php


namespace ParseThisNewsApi\Exception;


use ParseThisNewsApi\Util\HTTPCodes;

class ValidationException extends \Exception
{
    protected $code = HTTPCodes::BAD_REQUEST;
}