<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

abstract class Parameter implements SpecNodeInterface
{
    public string $name;
    public bool $required;
    public string $description;
    public string $type;
    public ?Method $parent;

    public function __construct(string $name, string $description, bool $required = true, string $type = 'string')
    {
        $this->name = $name;
        $this->description = $description;
        $this->required = $required;
        $this->type = $type;
    }

    abstract public function finalize(): array;
}
