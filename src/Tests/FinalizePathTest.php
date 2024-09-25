<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Method;
use Lucite\ApiSpec\Path;
use PHPUnit\Framework\TestCase;

class FinalizePathTest extends TestCase
{
    public function testFinalizePath(): void
    {
        $obj = Path::create('/testurl')
            ->addMethod(Method::create('get', 'get method summary', 'getSomething'))
            ->addMethod(Method::create('post', 'post method summary', 'createSomething'));
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(2, count($keys));
        $this->assertEquals('get', $keys[0]);
        $this->assertEquals('post', $keys[1]);
    }
}
