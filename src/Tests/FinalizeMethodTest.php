<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Method;
use Lucite\ApiSpec\QueryParameter;
use PHPUnit\Framework\TestCase;

class FinalizeMethodTest extends TestCase
{
    public function testFinalizeMethodWithoutSchema(): void
    {
        $obj = Method::create('get', 'a get request', 'getResource');
        $finalized = $obj->finalize();
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
        $obj = Method::create('get', 'a get request', 'getResource', 'TestSchema');
        $finalized = $obj->finalize();

        $schema = $finalized['requestBody']['content']['application/json']['schema'];
        $this->assertEquals(
            '#/components/schemas/TestSchema',
            $schema['properties']['data']['$ref'],
        );
        $this->assertTrue($finalized['requestBody']['required']);
    }

    public function testFinalizeMethodWithQueryParameter(): void
    {
        $obj = Method::create('get', 'a get request', 'getResource', 'TestSchema');
        $obj
            ->addParameter(QueryParameter::create('param1', 'firstParameter', false, 'string'))
            ->addParameter(QueryParameter::create('param2', 'secondParameter', false, 'string'));
        $finalized = $obj->finalize();

        $this->assertEquals(2, count($finalized['parameters']));
        $this->assertEquals('param1', $finalized['parameters'][0]['name']);
        $this->assertEquals('param2', $finalized['parameters'][1]['name']);
    }
}
