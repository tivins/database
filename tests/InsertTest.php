<?php

use Tivins\Database\{ Query, SelectQuery };
use Tivins\Database\Tests\{ TestConfig, TestBase };
use PHPUnit\Framework\TestCase;

class InsertTest extends TestBase
{
    public function testInsert()
    {
        $query = TestConfig::db()
            ->insert('test')
            ->fields(['id' => 2])
            ;
        $this->checkQuery($query,
            'insert into `test` (`id`) values (?)', [2]);
    }
}