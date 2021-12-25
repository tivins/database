<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Query;
use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{
    protected function checkQuery(Query $query, string $expectedQueryString, array $expectedParams)
    {
        $query_data = json_encode($query->build());
        $expected_data = json_encode(['sql'=>$expectedQueryString, 'parameters'=>$expectedParams]);
        $this->assertEquals($expected_data, $query_data);
    }
}