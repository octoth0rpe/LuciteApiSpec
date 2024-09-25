<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Specification;
use PHPUnit\Framework\TestCase;

class FinalizeRestSpecificationTest extends TestCase
{
    public function testConvertedSpec(): void
    {
        $bookSchema = Schema::create('Book')
            ->addProperty(Property::create('bookId', ['type' => 'integer']))
            ->addProperty(Property::create('title'))
            ->addProperty(Property::create('description'));
        $authorSchema = Schema::create('Author')
            ->addProperty(Property::create('authorId', ['type' => 'integer']))
            ->addProperty(Property::create('bookId', ['type' => 'integer']))
            ->addProperty(Property::create('name'));
        $saleSchema = Schema::create('Sale')
            ->addProperty(Property::create('saleId', ['type' => 'integer']))
            ->addProperty(Property::create('bookId', ['type' => 'integer']))
            ->addProperty(Property::create('quantity', ['type' => 'integer']));

        $obj = new Specification('testspec', '1.2.3');
        $obj->addRestMethods('/books/', $bookSchema);
        $obj->addRestMethods('/authors/', $authorSchema);
        $obj->addRestMethods('/sales/', $saleSchema);

        $routes = [];
        foreach ($obj->generateRoutes() as $method => [$path, $operation]) {
            $routes[] = $method.'->'.$path;
        }
        sort($routes);

        $this->assertEquals('delete->/authors/{authorId}', $routes[0]);
        $this->assertEquals('delete->/books/{bookId}', $routes[1]);
        $this->assertEquals('delete->/sales/{saleId}', $routes[2]);
        $this->assertEquals('get->/authors/', $routes[3]);
        $this->assertEquals('get->/authors/{authorId}', $routes[4]);
        $this->assertEquals('get->/books/', $routes[5]);
        $this->assertEquals('get->/books/{bookId}', $routes[6]);
        $this->assertEquals('get->/sales/', $routes[7]);
        $this->assertEquals('get->/sales/{saleId}', $routes[8]);
        $this->assertEquals('patch->/authors/{authorId}', $routes[9]);
        $this->assertEquals('patch->/books/{bookId}', $routes[10]);
        $this->assertEquals('patch->/sales/{saleId}', $routes[11]);
        $this->assertEquals('post->/authors/', $routes[12]);
        $this->assertEquals('post->/books/', $routes[13]);
        $this->assertEquals('post->/sales/', $routes[14]);
    }
}
