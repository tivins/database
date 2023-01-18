<?php

namespace Tivins\Database\Tests;

use function PHPUnit\Framework\assertEquals;

class DropTest extends \PHPUnit\Framework\TestCase
{
    public function testDropAndShowTables()
    {
        $db = TestConfig::db();
        $db->dropAllTables();
        $tables = $db->getTables();
        assertEquals([], $tables);
    }
}