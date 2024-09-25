<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Response implements SpecNodeInterface
{
    public string $code;
    public string $description;
    public ?string $schema = null;

    public function __construct(string $code, string $description, ?string $schema = null)
    {
        $this->code = $code;
        $this->description = $description;
        $this->schema = $schema;
    }

    public static function create(string $code, string $description, ?string $schema = null): Response
    {
        return new Response($code, $description, $schema);
    }

    public function finalize(): array
    {
        $response = [
            'description' => $this->description,
        ];
        if ($this->code === '422' || str_starts_with($this->code, '5')) {
            $response['content'] = [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => [
                                'type' => 'boolean',
                                'const' => false,
                            ],
                            'warnings' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'type' => 'string',
                                ],
                            ],
                            'errors' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }
        if ($this->schema !== null) {

            if (str_starts_with($this->code, '2')) {
                $response['content'] = [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'success' => [
                                    'type' => 'boolean',
                                    'const' => true,
                                ],
                                'warnings' => [
                                    'type' => 'object',
                                    'additionalProperties' => [
                                        'type' => 'string',
                                    ],
                                ],
                                'data' => [
                                    '$ref' => '#/components/schemas/'.$this->schema,
                                ],
                            ],
                        ],
                    ],
                ];
            }
        }
        return $response;
    }
}
