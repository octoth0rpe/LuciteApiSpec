<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateStringTest extends TestCase
{
    public function testNotString(): void
    {
        $property = Property::create('prop', ['type' => 'string']);

        $data = ['prop' => false];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 4];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => []];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => null];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'validstring'];
        $this->assertTrue($property->validate($data));
    }

    public function testStringMinLength(): void
    {
        $property = Property::create('prop', ['type' => 'string', 'minLength' => 10]);

        $data = ['prop' => 'tooshort'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'thisislongenough'];
        $this->assertTrue($property->validate($data));
    }

    public function testStringMaxLength(): void
    {
        $property = Property::create('prop', ['type' => 'string', 'maxLength' => 12]);

        $data = ['prop' => 'thisistoolong'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'shortenough'];
        $this->assertTrue($property->validate($data));
    }

    public function testPattern(): void
    {
        $property = Property::create('prop', ['type' => 'string', 'pattern' => '/^foo$/']);

        $data = ['prop' => 'notfoo'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'foo'];
        $this->assertTrue($property->validate($data));
    }

    public function testEnum(): void
    {
        $property = Property::create('prop', ['type' => 'string', 'enum' => ['valid1', 'valid2']]);

        $data = ['prop' => 'notvalid'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'valid1'];
        $this->assertTrue($property->validate($data));

        $data = ['prop' => 'valid2'];
        $this->assertTrue($property->validate($data));
    }

    public function testConst(): void
    {
        $property = Property::create('prop', ['type' => 'string', 'const' => 'onlyvalid']);

        $data = ['prop' => 'notvalid'];
        $this->assertTrue(is_string($property->validate($data)));

        $data = ['prop' => 'onlyvalid'];
        $this->assertTrue($property->validate($data));
    }
}
