<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateObjectTest extends TestCase
{
    public function testNotObject(): void
    {
        $property = Property::create('prop', ['type' => 'object']);

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'hiya'];
        $this->assertTrue(is_string($property->validate($data)));

        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => null];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => true];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => []];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['va1', 'val2']];
        $this->assertTrue(is_string($property->validate($data)));


        $data = ['prop' => ['key1' => true]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => ['key1', 'key2' => true]];
        $this->assertTrue($property->validate($data));
    }

    public function testMinProperties(): void
    {
        $property = Property::create('prop', ['type' => 'object', 'minProperties' => 2]);

        $data = ['prop' => []];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['key1' => 1]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['key1' => 1, 'key2' => 2]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => ['key1' => 1, 'key2' => 2, 'key3' => 3]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => ['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 4]];
        $this->assertTrue($property->validate($data));
    }

    public function testMaxProperties(): void
    {
        $property = Property::create('prop', ['type' => 'object', 'maxProperties' => 2]);

        $data = ['prop' => ['key1' => 1, 'key2' => 2, 'key3' => 3, 'key4' => 4]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['key1' => 1, 'key2' => 2, 'key3' => 3]];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => ['key1' => 1, 'key2' => 2]];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => ['key1' => 1]];
        $this->assertTrue($property->validate($data));
    }
}
