<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Response implements SpecNodeInterface
{
    public string $code;
    public string $description;
    public ?string $schema = null;
    public bool $multiple;

    public function __construct(string $code, string $description, ?string $schema = null, bool $multiple = false)
    {
        $this->code = $code;
        $this->description = $description;
        $this->schema = $schema;
        $this->multiple = $multiple;
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
            $data = [
                '$ref' => '#/components/schemas/'.$this->schema,
            ];
            if ($this->multiple) {
                # $data = {"type":"array","items":{"$ref":"#\/components\/schemas\/Sale"}}
                $data = [
                    "type" => "array",
                    "items" => $data,
                ];
            }
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
                                'data' => $data,
                            ],
                        ],
                    ],
                ];
            }
        }
        return $response;
    }
}
