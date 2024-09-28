<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

use Lucite\Schema\Exception\SchemaNotFoundException;

class Specification implements SpecNodeInterface
{
    public static string $openApiVersion = '3.1.0';
    public string $title = '';
    public string $version = '';
    public ?string $description = null;

    protected array $paths = [];
    protected array $schemas = [];

    public function __construct(string $title, string $version, ?string $description = null)
    {
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
    }

    public function addSchema(Schema $newSchema, bool $addCreateVariant = true): Specification
    {
        $this->schemas[$newSchema->name] = $newSchema;

        if ($addCreateVariant) {
            $createSchema = clone $newSchema;
            $createSchema->name = $createSchema->name.'Create';
            array_shift($createSchema->properties);
            $this->schemas[$createSchema->name] = $createSchema;
        }

        return $this;
    }

    public function addErrorSchema(): Specification
    {
        return $this;
    }

    public function addPath(Path $newPath): Specification
    {
        $this->paths[$newPath->path] = $newPath;
        return $this;
    }

    public function getSchema(string $name): Schema
    {
        if (isset($this->schemas[$name])) {
            return $this->schemas[$name];
        }
        throw new SchemaNotFoundException();
    }

    public function addRestGet(string $url, Schema $schema): Specification
    {
        $primaryKey = $schema->primaryKey();

        if (isset($this->paths[$url]) === false) {
            $this->addPath(new Path($url));
        }
        $this->paths[$url]->addMethod(
            (new Method('get', 'Fetch a collection of '.$schema->name.' resources', 'get'.$schema->name.'Collection', $schema))
                ->addResponse(new Response('200', '', $schema->name, true))
        );

        $getOneUrl = $url.'{'.$primaryKey->name.'}';
        if (isset($this->paths[$getOneUrl]) === false) {
            $this->addPath(new Path($getOneUrl));
        }
        $this->paths[$getOneUrl]->addMethod(
            (new Method('get', 'Fetch a single '.$schema->name.' resource', 'get'.$schema->name, $schema))
                ->addParameter(new PathParameter($primaryKey->name, 'The '.$primaryKey->name.' of the resource to fetch', true, 'integer'))
                ->addResponse(new Response('200', '', $schema->name))
                ->addResponse(new Response('404', 'Not Found'))
        );

        return $this;
    }

    public function addRestPost(string $url, Schema $schema): Specification
    {
        $createSchema = null;
        foreach ($this->schemas as $possibleCreateSchema) {
            if ($possibleCreateSchema->isCreateSchemaFor($schema)) {
                $createSchema = $possibleCreateSchema;
            }
        }

        if (isset($this->paths[$url]) === false) {
            $this->addPath(new Path($url));
        }

        $this->paths[$url]->addMethod(
            (new Method('post', 'Create a new '.$schema->name.' resource', 'create'.$schema->name, $createSchema))
                ->addResponse(new Response('201', 'Successfully created', $schema->name))
                ->addResponse(new Response('401', 'Not Authorized'))
                ->addResponse(new Response('422', 'Validation Error'))
        );

        return $this;
    }

    public function addRestPatch(string $url, Schema $schema): Specification
    {
        $primaryKey = $schema->primaryKey();
        $url .= '{'.$primaryKey->name.'}';
        if (isset($this->paths[$url]) === false) {
            $this->addPath(new Path($url));
        }

        $this->paths[$url]->addMethod(
            (new Method('patch', 'Update an existing '.$schema->name.' resource', 'update'.$schema->name, $schema))
                ->addParameter(new PathParameter($primaryKey->name, 'The '.$primaryKey->name.' of the resource to fetch', true, 'integer'))
                ->addResponse(new Response('201', 'Successfully updated', $schema->name))
                ->addResponse(new Response('401', 'Not Authorized'))
                ->addResponse(new Response('404', 'Not Found'))
                ->addResponse(new Response('422', 'Validation Error'))
        );

        return $this;
    }

    public function addRestDelete(string $url, Schema $schema): Specification
    {
        $primaryKey = $schema->primaryKey();
        $url = $url.'{'.$primaryKey->name.'}';

        if (isset($this->paths[$url]) === false) {
            $this->addPath(new Path($url));
        }
        $this->paths[$url]->addMethod(
            (new Method('delete', 'Delete a '.$schema->name.' resource', 'delete'.$schema->name, $schema))
                ->addParameter(new PathParameter($primaryKey->name, 'The '.$primaryKey->name.' of the resource to delete', true, 'integer'))
                ->addResponse(new Response('204', 'Deleted'))
                ->addResponse(new Response('401', 'Not Authorized'))
                ->addResponse(new Response('404', 'Not Found'))
        );
        return $this;
    }

    public function addRestMethods(string $baseUrl, Schema $schema, bool $get = true, bool $post = true, bool $patch = true, bool $delete = true): Specification
    {
        $this->addSchema($schema);
        if ($get) {
            $this->addRestGet($baseUrl, $schema);
        }
        if ($post) {
            $this->addRestPost($baseUrl, $schema);
        }
        if ($patch) {
            $this->addRestPatch($baseUrl, $schema);
        }
        if ($delete) {
            $this->addRestDelete($baseUrl, $schema);
        }
        return $this;
    }

    public function generateRoutes()
    {
        foreach ($this->paths as $path) {
            foreach ($path->methods as $method) {
                $function = '';
                switch ($method->method) {
                    case 'get':
                        $function = ($method->operationId === 'get'.$method->schema->name)
                            ? 'getOne'
                            : 'getMany';
                        break;
                    case 'post':
                        $function = 'create';
                        break;
                    case 'patch':
                        $function = 'update';
                        break;
                    case 'delete':
                        $function = 'delete';
                        break;
                }
                $schemaName = $method->schema->name;
                if (str_ends_with($schemaName, 'Create')) {
                    $schemaName = substr($schemaName, 0, strlen($schemaName) - 6);
                }
                yield $method->method => [$path->path, $schemaName, $function];
            }
        }
    }

    /**
     * Creates final openapi specification structure as an array suitable for
     * passing to json_encode.
     * @return array
     */
    public function finalize(): array
    {
        $obj = [
            'openapi' => static::$openApiVersion,
            'info' => [
                'title' => $this->title,
                'version' => $this->version,
            ],
        ];

        if (is_null($this->description) === false) {
            $obj['info']['description'] = $this->description;
        }

        if (count($this->paths) > 0) {
            $obj['paths'] = [];
        }
        foreach ($this->paths as $name => $path) {
            $obj['paths'][$name] = $path->finalize();
        }

        if (count($this->schemas) > 0) {
            $obj['components'] = ['schemas' => []];
        }
        foreach ($this->schemas as $name => $schema) {
            $obj['components']['schemas'][$name] = $schema->finalize();
        }

        return $obj;
    }
}
