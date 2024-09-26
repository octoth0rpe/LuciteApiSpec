<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateArrayTest extends TestCase
{
    public function testNotArray(): void
    {
        $property = new Property('prop', 'array');

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'hiya'];
        $this->assertTrue(is_string($property->validate($data)));

        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => null];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => true];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['key1', 'key2' => true]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => []];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => ['va1', 'val2']];
        $this->assertTrue($property->validate($data));
    }

    public function testMinItems(): void
    {
        $property = new Property('prop', 'array', ['minItems' => 2]);

        $data = ['prop' => [1]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => [1, 2]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => [1, 2, 3]];
        $this->assertTrue($property->validate($data));
    }

    public function testMaxItems(): void
    {
        $property = new Property('prop', 'array', ['maxItems' => 3]);

        $data = ['prop' => [1, 2, 3, 4]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => [1, 2, 3]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => []];
        $this->assertTrue($property->validate($data));
    }


    public function testUniqueItems(): void
    {
        $propertyNotRequiringUnique = new Property('prop', 'array', ['uniqueItems' => false]);
        $data = ['prop' => [1, 2, 2]];
        $this->assertTrue($propertyNotRequiringUnique->validate($data));

        $data = ['prop' => [1, 2, 3]];
        $this->assertTrue($propertyNotRequiringUnique->validate($data));

        $propertyRequiringUnique = new Property('prop', 'array', ['uniqueItems' => true]);

        $data = ['prop' => [1, 2, 2]];
        $this->assertTrue(is_string($propertyRequiringUnique->validate($data)));

        $data = ['prop' => [1, 2, 3]];
        $this->assertTrue($propertyRequiringUnique->validate($data));
    }
}
