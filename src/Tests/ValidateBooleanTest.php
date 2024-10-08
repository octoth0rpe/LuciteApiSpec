<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateBooleanTest extends TestCase
{
    public function testNotBoolean(): void
    {
        $property = new Property('prop', 'boolean');

        $data = ['prop' => 6];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'hiya'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => []];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => null];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => true];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => false];
        $this->assertTrue($property->validate($data));
    }

    public function testEnum(): void
    {
        $property = new Property('prop', 'boolean', ['enum' => [true]]);

        $data = ['prop' => false];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => true];
        $this->assertTrue($property->validate($data));
    }

    public function testConst(): void
    {
        $property = new Property('prop', 'boolean', ['const' => false]);

        $data = ['prop' => true];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => false];
        $this->assertTrue($property->validate($data));
    }
}
