<?php

declare(strict_types=1);

namespace Lucite\ApiSpec\Tests;

use Lucite\ApiSpec\Schema;
use Lucite\ApiSpec\Property;
use PHPUnit\Framework\TestCase;

class ValidateOptionalTest extends TestCase
{
    public function testIgnoreMissingOptionalValues(): void
    {
        $schema = (new Schema('scheme1'))
            ->addProperty(new Property('id', 'number', [], false, true))
            ->addProperty(new Property('name', 'string', [], true))
            ->addProperty(new Property('description', 'string'));

        $data = ['name' => 'yep'];
        # description is optional, so $result should be true even though
        # there is no description value.
        $result = $schema->getValidator()->validate($data);
        $this->assertTrue($result);
    }

    public function testErrorOnMissingRequiredValues(): void
    {
        $schema = (new Schema('scheme1'))
            ->addProperty(new Property('id', 'number', [], false, true))
            ->addProperty(new Property('name', 'string', [], true))
            ->addProperty(new Property('description', 'string'));

        $data = [];
        $result = $schema->getValidator()->validate($data);
        # Because description is optional, there should only be
        # 1 error
        $this->assertEquals(1, count(array_keys($result)));
    }
}
