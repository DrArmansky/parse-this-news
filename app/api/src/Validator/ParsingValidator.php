<?php


namespace ParseThisNewsApi\Validator;


use ParseThisNewsApi\Exception\ValidationException;
use ParseThisNewsApi\Request\iRequest;

class ParsingValidator implements iValidator
{
    /**
     * @inheritDoc
     */
    public function validate(iRequest $request): void
    {
        if (empty($request->get('source'))) {
            throw new ValidationException("Empty value for source");
        }
    }
}