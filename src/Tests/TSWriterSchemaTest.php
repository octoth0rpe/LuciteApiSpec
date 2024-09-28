<?php
declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Specification;
use Lucite\ApiSpec\TypescriptWriter;
use PHPUnit\Framework\TestCase;

class TSWriterSchemaTest extends TestCase
{
    public function testSchemaIsExported(): void
    {
        $spec = new Specification('test');
        $spec->addSchema(new Schema('Test'));
        $tsWriter = new TypescriptWriter($spec);
        $schemaOuput = $tsWriter->convertSchema('Test');
        $this->assertStringStartsWith('export', $schemaOuput);
    }

    public function testSchemaHasCorrectName(): void
    {
        $spec = new Specification('test');
        $spec->addSchema(new Schema('Test'));
        $tsWriter = new TypescriptWriter($spec);
        $schemaOuput = $tsWriter->convertSchema('Test');
        $this->assertStringContainsString('TestSchema', $schemaOuput);
    }

    public function testCanOverwriteIndent(): void
    {
        $spec = new Specification('test');
        $spec->addSchema(
            (new Schema('Test'))
                ->addProperty(new Property('testId', type: 'number'))
        );
        $default = TypescriptWriter::$indentSize;
        TypescriptWriter::$indentSize = 7;
        $tsWriter = new TypescriptWriter($spec);
        $schemaOuput = $tsWriter->convertSchema('Test');
        TypescriptWriter::$indentSize = $default;
        $this->assertStringContainsString('       testId: number;', $schemaOuput);
    }

    public function testWritesBasicPropertyTypesCorrectly(): void
    {
        $spec = new Specification('test');
        $spec->addSchema(
            (new Schema('Test'))
                ->addProperty(new Property('testId', type: 'number'))
                ->addProperty(new Property('name', type: 'string'))
                ->addProperty(new Property('isDeleted', type: 'boolean'))
        );
        $tsWriter = new TypescriptWriter($spec);
        $schemaOuput = $tsWriter->convertSchema('Test');
        $this->assertStringContainsString('  testId: number;', $schemaOuput);
        $this->assertStringContainsString('  name: string;', $schemaOuput);
        $this->assertStringContainsString('  isDeleted: boolean;', $schemaOuput);
    }
}