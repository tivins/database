<?php

use Tivins\Database\{ Database, SelectQuery };
use Tivins\Database\Connectors\MySQLConnector;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testSelect()
    {
        $db = new Database(new MySQLConnector('test', 'travis', '', '127.0.0.1'));
        $query = $db->select('test', 't');
        $build = $query->build();
        $this->assertEquals(json_encode($build), '["select from test t",[]]');
    }
}