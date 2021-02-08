<?php


namespace ParseThisNewsApi\Exception;


use ParseThisNewsApi\Util\HTTPCodes;

class ConflictException extends \Exception
{
    protected $code = HTTPCodes::CONFLICT;
}