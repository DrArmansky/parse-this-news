<?php

namespace ParseThisNewsApi\Validator;


use ParseThisNewsApi\Exception\ValidationException;
use ParseThisNewsApi\Request\iRequest;

interface iValidator
{
    /**
     * @param iRequest $request
     * @throws ValidationException
     */
    public function validate(iRequest $request);
}
