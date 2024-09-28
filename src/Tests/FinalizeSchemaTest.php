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
        $schema = new Schema('scheme1');
        $finalized = $schema->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(1, count($keys));
        $this->assertEquals('type', $keys[0]);
        $this->assertEquals('object', $finalized['type']);
    }

    public function testConvertSchemaWithProperties(): void
    {
        $schema = new Schema('scheme1');
        $schema->addProperty(new Property('id'));
        $schema->addProperty(new Property('name'));
        $finalized = $schema->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(2, count($keys));
        $this->assertEquals('properties', $keys[0]);
        $this->assertEquals('type', $keys[1]);
        $this->assertEquals('object', $finalized['type']);
    }

    public function testRequired(): void
    {
        $schema = new Schema('scheme1');
        $schema->addProperty(new Property('id'));
        $schema->addProperty(new Property('name', required: true));
        $finalized = $schema->finalize();
        $keys = array_keys($finalized);
        sort($keys);
        
        $this->assertEquals(3, count($keys));
        $this->assertEquals('properties', $keys[0]);
        $this->assertEquals('required', $keys[1]);
        $this->assertEquals('type', $keys[2]);
        $this->assertEquals(1, count($finalized['required']));
        $this->assertEquals('name', $finalized['required'][0]);
    }
}
