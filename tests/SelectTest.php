<?php

namespace Tivins\Database\Tests;

class SelectTest extends TestBase
{
    public function testSelect()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addFields('t');
        $this->checkQuery($query,
            'select t.* from t_test `t`', []);
    }

    public function testSelectFieldWithoutAlias()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id');
        $this->checkQuery($query,
            'select t.`id` from t_test `t`', []);
    }

    public function testSelectFieldAlias()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id', 't_id');
        $this->checkQuery($query,
            'select t.`id` as t_id from t_test `t`', []);
    }

    public function testSelectJoin()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addField('t', 'id', 't_id')
            ->leftJoin('other', 'o', 'o.oid = t.id')
            ;
        $this->checkQuery($query,
            'select t.`id` as t_id from t_test `t` left join `t_other` `o` on o.oid = t.id', []);
    }

    public function testNull()
    {
        $query = TestConfig::db()
            ->select('test', 't')
            ->addFields('t')
            ->isNull('t.field')
            ->isNotNull('t.another_field')
            ;
        $this->checkQuery($query,
            'select t.* from t_test `t` where t.field is null and t.another_field is not null', []);
    }

    /*
    public function testSelectLike()
    {
        // To do
    }
    */
}