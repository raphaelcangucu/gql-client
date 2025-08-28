<?php

namespace RaphaelCangucu\GqlClient\Tests\Unit;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Enums\Format;
use RaphaelCangucu\GqlClient\Enums\Request;

class EnumsTest extends TestCase
{
    public function test_format_enum_constants()
    {
        $this->assertEquals('json', Format::JSON);
        $this->assertEquals('array', Format::ARRAY);
    }

    public function test_request_enum_constants()
    {
        $this->assertEquals('query', Request::QUERY);
        $this->assertEquals('mutation', Request::MUTATION);
        $this->assertEquals('raw', Request::RAW);
    }
}