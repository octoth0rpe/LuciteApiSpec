<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateNumberTest extends TestCase
{
    public function testNotNumber(): void
    {
        $property = new Property('prop', 'number');

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
        $property = new Property('prop', 'number', ['enum' => [2, 3]]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 2];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 3];
        $this->assertTrue($property->validate($data));
    }

    public function testConst(): void
    {
        $property = new Property('prop', 'number', ['const' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));
    }

    public function testMinimum(): void
    {
        $property = new Property('prop', 'number', ['minimum' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 7];
        $this->assertTrue($property->validate($data));
    }

    public function testExclusiveMinimum(): void
    {
        $property = new Property('prop', 'number', ['exclusiveMinimum' => 6]);

        $data = ['prop' => 1];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 7];
        $this->assertTrue($property->validate($data));
    }

    public function testMaximum(): void
    {
        $property = new Property('prop', 'number', ['maximum' => 6]);

        $data = ['prop' => 7];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 1];
        $this->assertTrue($property->validate($data));
    }

    public function testExclusiveMaximum(): void
    {
        $property = new Property('prop', 'number', ['exclusiveMaximum' => 6]);

        $data = ['prop' => 7];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 1];
        $this->assertTrue($property->validate($data));
    }

    public function testMultipleOf(): void
    {
        $property = new Property('prop', 'number', ['multipleOf' => 3]);

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
