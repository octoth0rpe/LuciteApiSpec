<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use PHPUnit\Framework\TestCase;

class FinalizeSchemaTest extends TestCase
{
    public function testConvertSchemaWithoutProperties(): void
    {
        $obj = new Schema('scheme1');
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(1, count($keys));
        $this->assertEquals('type', $keys[0]);
        $this->assertEquals('object', $finalized['type']);
    }

    public function testConvertSchemaWithProperties(): void
    {
        $obj = new Schema('scheme1');
        $obj->addProperty(new Property('id'));
        $obj->addProperty(new Property('name'));
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(2, count($keys));
        $this->assertEquals('properties', $keys[0]);
        $this->assertEquals('type', $keys[1]);
        $this->assertEquals('object', $finalized['type']);
    }
}
