<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class QueryParameter extends Parameter implements SpecNodeInterface
{
    public function finalize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'in' => 'query',
            'required' => $this->required,
            'schema' => [
                'type' => $this->type,
            ],
        ];
    }
}
