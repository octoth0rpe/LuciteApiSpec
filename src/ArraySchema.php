<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class ArraySchema extends Schema implements SpecNodeInterface
{
    public string $name;
    public string $baseSchema;

    public function __construct(string $name, string $baseSchema)
    {
        $this->name = $name;
        $this->baseSchema = $baseSchema;
    }

    public function usesBaseSchema(Schema $baseSchema): bool
    {
        return $this->baseSchema === $baseSchema->name;
    }

    public function finalize(): array
    {
        return [
            'type' => 'array',
            'items' => [
                '$ref' => '#/components/schemas/'.$this->baseSchema,
            ],
        ];
    }
}
