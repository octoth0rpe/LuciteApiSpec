<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Property implements SpecNodeInterface
{
    public string $name;
    public array $details;

    public function __construct(string $name, array $details = ['type' => 'string'])
    {
        $this->name = $name;
        $this->details = $details;
    }

    public static function create(string $name, array $details = ['type' => 'string']): Property
    {
        return new Property($name, $details);
    }

    public function finalize(): array
    {
        return $this->details;
    }
}
