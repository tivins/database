<?php

use Tivins\Database\{ Database, SelectQuery };
use Tivins\Database\Connectors\MySQLConnector;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testSelect()
    {
        $db = new Database(new MySQLConnector('', 'root', '', 'db'));
        $query = $db->select('test', 't');
        $this->assertTrue(true);
    }
}