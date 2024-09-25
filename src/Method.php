<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Method implements SpecNodeInterface
{
    public string $method;
    public string $summary;
    public string $operationId;
    public ?Schema $schema;
    public array $responses = [];
    public array $parameters = [];

    public function __construct(string $method, string $summary, string $operationId, ?Schema $schema = null)
    {
        $this->method = $method;
        $this->summary = $summary;
        $this->operationId = $operationId;
        $this->schema = $schema;
    }

    public function finalize(): array
    {
        $finalized = [
            'summary' => $this->summary,
            'operationId' => $this->operationId,
        ];
        if ($this->schema !== null && in_array($this->method, ['post', 'patch'])) {
            $finalized["requestBody"] = [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    '$ref' => '#/components/schemas/'.$this->schema->name,
                                ],
                            ],
                        ],
                    ],
                ],
                'required' => true,
            ];
        }
        if (count($this->responses) > 0) {
            $finalized['responses'] = [];
            foreach ($this->responses as $code => $response) {
                $finalized['responses'][$code] = $response->finalize();
            }
        }
        if (count($this->parameters) > 0) {
            $finalized['parameters'] = array_map(function ($param) {
                return $param->finalize();
            }, $this->parameters);
        }
        return $finalized;
    }

    public function addResponse(Response $newResponse): Method
    {
        $this->responses[$newResponse->code] = $newResponse;
        return $this;
    }

    public function addParameter(Parameter $newParameter): Method
    {
        $this->parameters[] = $newParameter;
        return $this;
    }

    public static function create(string $method, string $summary, string $operationId, ?Schema $schema = null): Method
    {
        return new Method($method, $summary, $operationId, $schema);
    }
}
