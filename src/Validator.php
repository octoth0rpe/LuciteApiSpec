<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Validator
{
    public array $properties = [];
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function validate(array &$data): array | bool
    {
        $errors = [];
        foreach ($this->properties as $property) {
            $result = $property->validate($data);
            if (is_string($result)) {
                $errors[$property->name] = $result;
            }
        }
        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }
}
