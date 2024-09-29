<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Specification;
use PHPUnit\Framework\TestCase;

class AddRestDeleteTest extends TestCase
{
    public function testAddRestDelete(): void
    {
        $book = (new Schema('Book'))
            ->addProperty(new Property(name: 'bookId', type: 'integer', primaryKey: true))
            ->addProperty(new Property(name: 'title', rules: ['minLength' => 1]))
            ->addProperty(new Property(name: 'description', type: 'string'))
            ->addProperty(new Property(name: 'createdOn', readOnly: true));

        $obj = new Specification('testspec', '1.0.0');
        $obj->addSchema($book);
        $obj->addRestDelete('/books/', $book);
        $finalized = $obj->finalize();

        $root_attributes = array_keys($finalized);
        sort($root_attributes);
        $this->assertEquals(4, count($root_attributes));
        $this->assertEquals('components', $root_attributes[0]);
        $this->assertEquals('info', $root_attributes[1]);
        $this->assertEquals('openapi', $root_attributes[2]);
        $this->assertEquals('paths', $root_attributes[3]);

        $paths = array_keys($finalized['paths']);
        sort($paths);
        $this->assertEquals(1, count($paths));
        $this->assertEquals('/books/{bookId}', $paths[0]);
        $this->assertEquals('delete', array_keys($finalized['paths'][$paths[0]])[0]);
    }
}
