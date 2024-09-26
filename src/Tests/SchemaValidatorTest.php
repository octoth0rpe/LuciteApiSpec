<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use PHPUnit\Framework\TestCase;

class SchemaValidatorTest extends TestCase
{
    public function testRulesAreApplied(): void
    {
        $schema = (new Schema('scheme1'))
            ->addProperty(new Property('id', 'number'))
            ->addProperty(new Property('name', 'string', ['minLength' => 1, 'maxLength' => 5]))
            ->addProperty(new Property('tags', 'array', ['maxItems' => 3, 'uniqueItems' => true]))
            ->addProperty(new Property('public', 'boolean'));
        $validator = $schema->getValidator();

        $data = [
            'name' => '',
            'tags' => [0, 1, 2, 3, 4],
            'public' => 'yes',
        ];
        $results = $validator->validate($data);
        $this->assertEquals(3, count(array_keys($results)));

        $data = [
            'name' => '',
            'tags' => [0, 1, 2, 3, 4],
            'public' => true,
        ];
        $results = $validator->validate($data);
        $this->assertEquals(2, count(array_keys($results)));

        $data = [
            'name' => '',
            'tags' => [0, 1],
            'public' => true,
        ];
        $results = $validator->validate($data);
        ;
        $this->assertEquals(1, count(array_keys($results)));

        $data = [
            'name' => 'long',
            'tags' => [0, 1],
            'public' => true,
        ];
        $results = $validator->validate($data);
        $this->assertTrue($results);
    }

    public function testStringsAreTrimmed(): void
    {
        $schema = (new Schema('scheme1'))
            ->addProperty(new Property('id', 'number'))
            ->addProperty(new Property('name'));
        $validator = $schema->getValidator();

        $data = ['name' => '   test'];
        $results = $validator->validate($data);
        $this->assertEquals(4, strlen($data['name']));
    }

}
