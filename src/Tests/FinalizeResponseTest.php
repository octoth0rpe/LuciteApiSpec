<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Response;
use PHPUnit\Framework\TestCase;

class FinalizeResponseTest extends TestCase
{
    public function testFinalizeResponseWithoutSchema(): void
    {
        $obj = new Response('200', 'a successful response');
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(1, count($keys));
        $this->assertEquals('description', $keys[0]);
        $this->assertEquals('a successful response', $finalized['description']);
    }

    public function testFinalizeResponseWithSchema(): void
    {
        $obj = new Response('200', 'a successful response with schema', 'TestSchema');
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(2, count($keys));
        $this->assertEquals('content', $keys[0]);
        $this->assertEquals('description', $keys[1]);
        $this->assertEquals('a successful response with schema', $finalized['description']);

        $this->assertEquals(
            '#/components/schemas/TestSchema',
            $finalized['content']['application/json']['schema']['properties']['data']['$ref'],
        );
    }
}
