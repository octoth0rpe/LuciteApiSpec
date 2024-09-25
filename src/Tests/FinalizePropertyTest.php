<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class FinalizePropertyTest extends TestCase
{
    public function testFinalizePath(): void
    {
        $obj = Property::create('id', ['type' => 'string']);
        $finalized = $obj->finalize();
        $keys = array_keys($finalized);
        sort($keys);

        $this->assertEquals(1, count($keys));
        $this->assertEquals('string', $finalized['type']);
    }
}
