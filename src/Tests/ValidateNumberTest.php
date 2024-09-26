<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateNumberTest extends TestCase
{
    public function testNotNumber(): void
    {
        $property = Property::create('prop', ['type' => 'number']);

        $data = ['prop' => false];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'hiya'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => []];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => null];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));
    }

    public function testEnum(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'enum' => [2, 3]]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 2];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 3];
        $this->assertTrue($property->validate($data));
    }

    public function testConst(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'const' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));
    }

    public function testMinimum(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'minimum' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 7];
        $this->assertTrue($property->validate($data));
    }

    public function testExclusiveMinimum(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'exclusiveMinimum' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 7];
        $this->assertTrue($property->validate($data));
    }

    public function testMaximum(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'maximum' => 6]);

        $data = ['prop' => 7];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 1];
        $this->assertTrue($property->validate($data));
    }

    public function testExclusiveMaximum(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'exclusiveMaximum' => 6]);

        $data = ['prop' => 7];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 1];
        $this->assertTrue($property->validate($data));
    }

    public function testMultipleOf(): void
    {
        $property = Property::create('prop', ['type' => 'number', 'multipleOf' => 3]);

        $data = ['prop' => 2];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 4];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 0];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 3];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));
    }
}
