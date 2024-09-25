<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Specification;
use PHPUnit\Framework\TestCase;

class AddRestPostTest extends TestCase
{
    public function testAddRestPost(): void
    {
        $book = Schema::create('Book')
            ->addProperty(Property::create('bookId', ['type' => 'integer']))
            ->addProperty(Property::create('title'))
            ->addProperty(Property::create('description'));

        $obj = new Specification('testspec', '1.0.0');
        $obj->addSchema($book);
        $obj->addRestPost('/books/', $book);
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
        $this->assertEquals('/books/', $paths[0]);
        $this->assertEquals('post', array_keys($finalized['paths'][$paths[0]])[0]);
    }
}
