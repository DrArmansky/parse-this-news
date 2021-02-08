<?php


namespace ParseThisNewsApi\Validator;


use ParseThisNewsApi\Exception\ValidationException;
use ParseThisNewsApi\Request\iRequest;

class UsingModelValidator implements iValidator
{
    /** @var \ReflectionProperty[] */
    protected array $properties;

    public function __construct(string $modelClass)
    {
        $this->properties = (new \ReflectionClass($modelClass))->getProperties();
    }

    /**
     * @param iRequest $request
     * @throws ValidationException
     */
    public function validate(iRequest $request): void
    {
        foreach ($this->properties as $property) {
            $propertyType = $property->getType();
            if ($propertyType === null || $propertyType->allowsNull()) {
                continue;
            }

            if (empty($request->get($property->getName()))) {
                throw new ValidationException("Empty value for " . $property->getName());
            }
        }
    }
}