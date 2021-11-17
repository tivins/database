<?php

use Tivins\Database\{ Query, SelectQuery };
use Tivins\Database\Tests\{ TestConfig, TestBase };
use PHPUnit\Framework\TestCase;

class SelectTest extends TestBase
{
    public function testSelect()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addFields('t');
        $this->checkQuery($query,
            'select t.* from test `t`', []);
    }

    public function testSelectFieldWithoutAlias()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id');
        $this->checkQuery($query,
            'select t.`id` from test `t`', []);
    }

    public function testSelectFieldAlias()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id', 't_id');
        $this->checkQuery($query,
            'select t.`id` as t_id from test `t`', []);
    }

    public function testSelectJoin()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id', 't_id')
            ->leftJoin('other', 'o', 'o.oid = t.id')
            ;
        $this->checkQuery($query,
            'select t.`id` as t_id from test `t` left join `other` `o` on o.oid = t.id', []);
    }
}