<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class PathParameter extends Parameter implements SpecNodeInterface
{
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
