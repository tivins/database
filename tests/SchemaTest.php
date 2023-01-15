<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Exceptions\ConnectionException;


class SchemaTest extends TestCase
{
    /**
     * @throws ConnectionException
     */
    public function testBasicSchema()
    {
        // $db    = TestConfig::db();
        // $build = new Builder(MyTable::class);
        // var_dump($build->getTableName());
        self::assertEquals(true, true);
    }
}