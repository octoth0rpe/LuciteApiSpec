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
                case 'integer':
                    $properties .= 'number';
                    break;
                case 'string':
                case 'number':
                case 'integer':
                case 'boolean':
                case 'null':
                    $properties .= $prop->type;
                    break;
                    # TODO: figure out array/object types
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


    public function convert(): string
    {
        $output = "\n";
        foreach ($this->spec->schemas as $schema) {
            $output .= $this->convertSchema($schema->name)."\n";
        }
        $output .= "\nexport const api = {};\n\n";

        $schemasAdded = [];
        foreach ($this->spec->paths as $path) {
            foreach ($path->methods as $method) {
                $httpMethod = $method->method;
                $tsSchemaName = $method->schema->name.'Schema';
                $output .= ('// '.$path->path.' -> '.$httpMethod.' -> '.$tsSchemaName."\n");
            }
        }
        /*
        foreach ($this->spec->generateRoutes() as $method => $details) {
            [$path, $schemaName, $action] = $details;
            $camelCaseName = lcfirst($schemaName);
            if (isset($schemasAdded[$camelCaseName]) === false) {
                $output .= "api['{$camelCaseName}'] = {};\n";
                $schemasAdded[$camelCaseName] = true;
            }
            print_r([$method, $details]);
            # $output .= $this->convertSchema($schema->name)."\n";
            }*/

        return $output;
    }
}
