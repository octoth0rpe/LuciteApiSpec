<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class TypescriptWriter
{
    public Specification $spec;
    public static int $indentSize = 2;
    protected string $indent1;
    protected string $indent2;
    protected string $indent3;

    public function __construct(Specification $spec)
    {
        $this->spec = $spec;
        $this->indent1 = str_repeat(' ', static::$indentSize);
        $this->indent2 = str_repeat(' ', static::$indentSize * 2);
        $this->indent3 = str_repeat(' ', static::$indentSize * 3);
    }

    public function convertSchema(string $schemaName): string
    {
        $schema = $this->spec->getSchema($schemaName);
        $properties = '';
        foreach ($schema->properties as $prop) {
            $properties .= $this->indent1;
            $properties .= $prop->name.': ';
            switch ($prop->type) {
                case 'string':
                case 'number':
                case 'boolean':
                case 'null':
                    $properties .= $prop->type;
                    break;

            }
            $properties .= ";\n";
        }
        return <<<TYPESCRIPT
export interface {$schema->name}Schema {
{$properties}};
TYPESCRIPT;
        return $schema;
    }

    public function convertRoute(string $method, string $path, string $operation, Schema $schema): string
    {
        $templatedPath = str_replace('{', '${', $path);

        return <<<TYPESCRIPT
api.{$operation} = () => {
    
};
TYPESCRIPT;
    }
}
