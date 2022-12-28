<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Exceptions\ConnectionException;
use Tivins\Database\Map\Builder;
use Tivins\Database\Tests\data\schema\MyTable;


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