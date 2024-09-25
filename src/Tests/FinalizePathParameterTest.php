<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\PathParameter;
use PHPUnit\Framework\TestCase;

class FinalizePathParameterTest extends TestCase
{
    public function testFinalizePathParameter(): void
    {
        $obj = PathParameter::create('testParam', 'parameter passed in querystring', false, 'integer');
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(5, count($keys));
        $this->assertEquals('description', $keys[0]);
        $this->assertEquals('in', $keys[1]);
        $this->assertEquals('name', $keys[2]);
        $this->assertEquals('required', $keys[3]);
        $this->assertEquals('schema', $keys[4]);
        $this->assertEquals('path', $finalized['in']);
    }
}
