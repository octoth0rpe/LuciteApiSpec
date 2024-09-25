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

    public function validate(array $data): array | bool
    {
        return true;
    }
}
