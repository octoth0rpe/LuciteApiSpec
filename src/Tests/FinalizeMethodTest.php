<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Method;
use Lucite\ApiSpec\QueryParameter;
use Lucite\ApiSpec\Schema;
use PHPUnit\Framework\TestCase;

class FinalizeMethodTest extends TestCase
{
    public function testFinalizeMethodWithoutSchema(): void
    {
        $method = new Method('get', 'a get request', 'getResource');
        $finalized = $method->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(2, count($keys));
        $this->assertEquals('operationId', $keys[0]);
        $this->assertEquals('summary', $keys[1]);
        $this->assertEquals('a get request', $finalized['summary']);
        $this->assertEquals('getResource', $finalized['operationId']);
    }

    public function testFinalizeMethodWithSchema(): void
    {
        $schema = new Schema('Test');
        $method = new Method('post', 'a get request', 'getResource', $schema);
        $finalized = $method->finalize();

        $schema = $finalized['requestBody']['content']['application/json']['schema'];
        $this->assertEquals(
            '#/components/schemas/Test',
            $schema['properties']['data']['$ref'],
        );
        $this->assertTrue($finalized['requestBody']['required']);
    }

    public function testFinalizeMethodWithQueryParameter(): void
    {
        $schema = new Schema('Test');
        $method = new Method('get', 'a get request', 'getResource', $schema);
        $method
            ->addParameter(new QueryParameter('param1', 'firstParameter', false, 'string'))
            ->addParameter(new QueryParameter('param2', 'secondParameter', false, 'string'));
        $finalized = $method->finalize();

        $this->assertEquals(2, count($finalized['parameters']));
        $this->assertEquals('param1', $finalized['parameters'][0]['name']);
        $this->assertEquals('param2', $finalized['parameters'][1]['name']);
    }
}
