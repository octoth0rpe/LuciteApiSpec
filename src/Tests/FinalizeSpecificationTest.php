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
        $obj = new Specification('testspec', '1.0.0');
        $obj
            ->addSchema(Schema::create('Book'), false, false)
            ->addSchema(Schema::create('Author'), false, false)
            ->addSchema(Schema::create('Sale'), false, false);

        $finalized = $obj->finalize();

        $schema_keys = array_keys($finalized['components']['schemas'] ?? []);
        sort($schema_keys);

        $this->assertEquals(3, count($schema_keys));
        $this->assertEquals('Author', $schema_keys[0]);
        $this->assertEquals('Book', $schema_keys[1]);
        $this->assertEquals('Sale', $schema_keys[2]);
    }

    public function testConvertedSpecHasSchemasWithVariants(): void
    {
        $obj = new Specification('testspec', '1.0.0');
        $obj
            ->addSchema(Schema::create('Book'), true, true)
            ->addSchema(Schema::create('Author'), true, true)
            ->addSchema(Schema::create('Sale'), true, true);

        $finalized = $obj->finalize();

        $schema_keys = array_keys($finalized['components']['schemas'] ?? []);
        sort($schema_keys);

        $this->assertEquals(9, count($schema_keys));
        $this->assertEquals('Author', $schema_keys[0]);
        $this->assertEquals('AuthorCreate', $schema_keys[1]);
        $this->assertEquals('AuthorList', $schema_keys[2]);
        $this->assertEquals('Book', $schema_keys[3]);
        $this->assertEquals('BookCreate', $schema_keys[4]);
        $this->assertEquals('BookList', $schema_keys[5]);
        $this->assertEquals('Sale', $schema_keys[6]);
        $this->assertEquals('SaleCreate', $schema_keys[7]);
        $this->assertEquals('SaleList', $schema_keys[8]);
    }
}
