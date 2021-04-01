<?php

use Tivins\Database\{ Database, SelectQuery };
use Tivins\Database\Connectors\MySQLConnector;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testSelect()
    {
        $db = new Database(new MySQLConnector('test', 'travis', '', '127.0.0.1'));

        $query = $db->select('test', 't')->addFields('t');
        $build_data = json_encode($query->build());
        $this->assertEquals($build_data, '["select t.* from test t",[]]');
    }
}