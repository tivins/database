<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ ConnectionException, DatabaseException };
use Tivins\Database\Tests\data\Fruits;


class CreateTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testCreate()
    {
        $db = TestConfig::db();

        $db->dropTable('sample');

        $query = $db->create('sample')
            ->addAutoIncrement(name: 'id')
            ->addInteger('counter', 0, unsigned: true, nullable: false)
            ->addInteger('null_val', null, nullable: false)
            ->addGeometry('geom_field')
            ->addJSON('data')
            ->addIndex(['null_val']);

        $this->checkQuery($query,
            'create table if not exists `t_sample` ('
            . '`id` int unsigned auto_increment, '
            . '`counter` int unsigned not null default 0, '
            . '`null_val` int default null, '
            . '`geom_field` geometry, '
            . '`data` json default null, '
            . 'primary key (id), '
            . 'index (null_val)'
            . ')'
            , []);

        $query->execute();
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testCreateEnum()
    {
        $db = TestConfig::db();

        $db->dropTable('sample');
        $query = $db->create('sample')
            ->addAutoIncrement(name: 'id')
            ->addEnum('fruits', Fruits::cases());
        
        $this->checkQuery($query, 
            'create table if not exists `t_sample` (`id` int unsigned auto_increment, `fruits` enum("apple","banana","peach"), primary key (id))', 
            []);
        
        $query->execute();
        $this->assertTrue(true);
    }        

}