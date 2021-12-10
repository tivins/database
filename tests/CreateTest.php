<?php

namespace Tivins\Database\Tests;

class CreateTest extends TestBase
{
    public function testCreate()
    {
        $db = TestConfig::db();

        $db->dropTable('sample');

        $query = $db->create('sample')
            ->addAutoIncrement('id')
            ;

        $this->checkQuery($query,
            'create table if not exists `t_sample` (`id` int unsigned auto_increment, primary key (id))', []);

        $query->execute();
    }
}