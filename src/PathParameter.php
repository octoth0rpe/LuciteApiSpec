<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class PathParameter extends Parameter implements SpecNodeInterface
{
    public static function create(string $name, string $description, bool $required = true, string $type = 'string'): PathParameter
    {
        return new PathParameter($name, $description, $required, $type);
    }

    public function finalize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'in' => 'path',
            'required' => $this->required,
            'schema' => [
                'type' => $this->type,
            ],
        ];
    }
}
