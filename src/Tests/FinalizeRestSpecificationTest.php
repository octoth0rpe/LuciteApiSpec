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
        $bookSchema = (new Schema('Book'))
            ->addProperty(new Property('bookId', 'number'))
            ->addProperty(new Property('title', 'string', ['minLength' => 1, 'maxLength' => 255]))
            ->addProperty(new Property('description', 'string', ['minLength' => 0, 'maxLength' => 8000]));
        $authorSchema = (new Schema('Author'))
            ->addProperty(new Property('authorId', 'number'))
            ->addProperty(new Property('bookId', 'number'))
            ->addProperty(new Property('name'));
        $saleSchema = (new Schema('Sale'))
            ->addProperty(new Property('saleId', 'number'))
            ->addProperty(new Property('bookId', 'number'))
            ->addProperty(new Property('quantity', 'number'));

        $spec = new Specification('testspec', '1.2.3');
        $spec->addRestMethods('/books/', $bookSchema);
        $spec->addRestMethods('/authors/', $authorSchema);
        $spec->addRestMethods('/sales/', $saleSchema);

        $routes = [];
        foreach ($spec->generateRoutes() as $method => $details) {
            [$path, $schemaName, $function] = $details;
            $routes[] = strtoupper($method).' '.$path." -> $schemaName:$function";
        }
        sort($routes);
        #echo("\n--------\n");
        #echo(json_encode($obj->finalize()));
        #echo("\n--------\n");


        $this->assertEquals('DELETE /authors/{authorId} -> Author:delete', $routes[0]);
        $this->assertEquals('DELETE /books/{bookId} -> Book:delete', $routes[1]);
        $this->assertEquals('DELETE /sales/{saleId} -> Sale:delete', $routes[2]);
        $this->assertEquals('GET /authors/ -> Author:getMany', $routes[3]);
        $this->assertEquals('GET /authors/{authorId} -> Author:getOne', $routes[4]);
        $this->assertEquals('GET /books/ -> Book:getMany', $routes[5]);
        $this->assertEquals('GET /books/{bookId} -> Book:getOne', $routes[6]);
        $this->assertEquals('GET /sales/ -> Sale:getMany', $routes[7]);
        $this->assertEquals('GET /sales/{saleId} -> Sale:getOne', $routes[8]);
        $this->assertEquals('PATCH /authors/{authorId} -> Author:update', $routes[9]);
        $this->assertEquals('PATCH /books/{bookId} -> Book:update', $routes[10]);
        $this->assertEquals('PATCH /sales/{saleId} -> Sale:update', $routes[11]);
        $this->assertEquals('POST /authors/ -> Author:create', $routes[12]);
        $this->assertEquals('POST /books/ -> Book:create', $routes[13]);
        $this->assertEquals('POST /sales/ -> Sale:create', $routes[14]);
    }
}
