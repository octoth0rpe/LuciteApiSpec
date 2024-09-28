<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

use Lucite\ApiSpec\Specification;

class Schema implements SpecNodeInterface
{
    public string $name;
    public array $properties = [];
    public ?Specification $parent;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function usesBaseSchema(Schema $baseSchema): bool
    {
        return false;
    }

    public function getValidator(): Validator
    {
        if (count($this->properties) === 0) {
            throw new \Exception('Schema does not have any properties defined');
        }
        return new Validator(array_slice($this->properties, 1));
    }

    public function addProperty(Property $newProperty): Schema
    {
        $this->properties[] = $newProperty;
        $newProperty->parent = $this;
        return $this;
    }

    public function primaryKey(): Property
    {
        foreach ($this->properties as $prop) {
            if ($prop->primaryKey) {
                return $prop;
            }
        }
        throw new \Exception('Schema does not have any properties defined');
    }

    public function finalize(): array
    {
        $finalized = [
            'type' => 'object',
        ];
        $required = [];
        foreach ($this->properties as $prop) {
            if ($prop->required) {
                $required[] = $prop->name;
            }
        }
        if (count($required) > 0) {
            $finalized['required'] = $required;
        }

        if (count($this->properties) > 0) {
            $finalized['properties'] = [];
            foreach ($this->properties as $property) {
                $finalized['properties'][$property->name] = $property->finalize();
            }
        }
        return $finalized;
    }
}
