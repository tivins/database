<?php

use Tivins\Database\{ Database, Query, SelectQuery };
use Tivins\Database\Connectors\MySQLConnector;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    private Database $db;

    public function setUp(): void
    {
        if (isset($this->db)) return;
        $this->db = new Database(
            new MySQLConnector(
                getenv('DBNAME'),
                getenv('DBUSER'),
                getenv('DBPASS'),
                getenv('DBHOST'),
            )
        );
    }

    private function checkQuery(Query $query, string $sql, array $params)
    {
        $query_data = json_encode($query->build());
        $expected_data = json_encode([$sql, $params]);
        $this->assertEquals($query_data, $expected_data);
    }

    public function testSelect()
    {
        $query = $this->db
            ->select('test', 't')
            ->addFields('t');
        $this->checkQuery($query,
            'select t.* from test t', []);
    }

    public function testSelectFieldWithoutAlias()
    {
        $query = $this->db
            ->select('test', 't')
            ->addField('t', 'id');
        $this->checkQuery($query,
            'select t.`id` from test t', []);
    }

    public function testSelectFieldAlias()
    {
        $query = $this->db
            ->select('test', 't')
            ->addField('t', 'id', 't_id');
        $this->checkQuery($query,
            'select t.`id` as t_id from test t', []);
    }
}