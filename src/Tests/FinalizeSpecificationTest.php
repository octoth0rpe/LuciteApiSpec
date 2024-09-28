<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Specification;
use PHPUnit\Framework\TestCase;

class FinalizeSpecificationTest extends TestCase
{
    public function testConvertedSpecHasCorrectRootAttributes(): void
    {
        $obj = new Specification('testspec', '1.0.0');
        $finalized = $obj->finalize();
        $root_attributes = array_keys($finalized);
        sort($root_attributes);

        $this->assertEquals(2, count($root_attributes));
        $this->assertEquals('info', $root_attributes[0]);
        $this->assertEquals('openapi', $root_attributes[1]);
    }

    public function testConvertedSpecHasCorrectTitleAndVersion(): void
    {
        $obj = new Specification('testspec', '1.0.0');
        $finalized = $obj->finalize();

        $info_keys = array_keys($finalized['info'] ?? []);
        $this->assertEquals(2, count($info_keys));
        $this->assertEquals('testspec', $finalized['info']['title'] ?? '');
        $this->assertEquals('1.0.0', $finalized['info']['version'] ?? '');
    }

    public function testConvertedSpecHasSchemas(): void
    {
        $spec = new Specification('testspec', '1.0.0');
        $spec
            ->addSchema(new Schema('Book'), false)
            ->addSchema(new Schema('Author'), false)
            ->addSchema(new Schema('Sale'), false);

        $finalized = $spec->finalize();

        $schema_keys = array_keys($finalized['components']['schemas'] ?? []);
        sort($schema_keys);

        $this->assertEquals(3, count($schema_keys));
        $this->assertEquals('Author', $schema_keys[0]);
        $this->assertEquals('Book', $schema_keys[1]);
        $this->assertEquals('Sale', $schema_keys[2]);
    }

    public function testConvertedSpecHasSchemasWithVariants(): void
    {
        $spec = new Specification('testspec', '1.0.0');
        $spec
            ->addSchema(new Schema('Book'), true)
            ->addSchema(new Schema('Author'), true)
            ->addSchema(new Schema('Sale'), true);

        $finalized = $spec->finalize();

        $schema_keys = array_keys($finalized['components']['schemas'] ?? []);
        sort($schema_keys);

        $this->assertEquals(3, count($schema_keys));
        $this->assertEquals('Author', $schema_keys[0]);
        $this->assertEquals('Book', $schema_keys[1]);
        $this->assertEquals('Sale', $schema_keys[2]);
    }
}
