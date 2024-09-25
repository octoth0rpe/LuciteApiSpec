<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class QueryParameter extends Parameter implements SpecNodeInterface
{
    public static function create(string $name, string $description, bool $required = true, string $type = 'string'): QueryParameter
    {
        return new QueryParameter($name, $description, $required, $type);
    }

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
