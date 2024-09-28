<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

use Lucite\ApiSpec\Method;

class Path implements SpecNodeInterface
{
    public string $path;
    public array $methods = [];
    public ?Method $parent;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function addMethod(Method $newMethod): Path
    {
        $this->methods[$newMethod->method] = $newMethod;
        $newMethod->parent = $this;
        return $this;
    }

    public function finalize(): array
    {
        $finalized = [];
        foreach ($this->methods as $method => $obj) {
            $finalized[$method] = $obj->finalize();
        }
        return $finalized;
    }
}
