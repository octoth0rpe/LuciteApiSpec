<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Path implements SpecNodeInterface
{
    public string $path;
    public array $methods = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function addMethod(Method $newMethod): Path
    {
        $this->methods[$newMethod->method] = $newMethod;
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
