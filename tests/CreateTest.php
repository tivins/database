<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ ConnectionException, DatabaseException };
use Tivins\Database\Tests\data\Fruits;


class CreateTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testConnectorType()
    {
        $db = TestConfig::db();
        self::assertEquals('mysql', $db->getConnectorType());
    }

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
            ->addText('body')
            ->addPointer('reference_id')
            ->addIndex(['null_val']);

        $currentEngine = $query->getEngine();
        $this->assertEquals('InnoDB', $currentEngine);
        $this->checkQuery($query,
            'create table if not exists `t_sample` ('
            . '`id` int unsigned auto_increment, '
            . '`counter` int unsigned not null default 0, '
            . '`null_val` int default null, '
            . '`geom_field` geometry, '
            . '`data` json default null, '
            . '`body` text, '
            . '`reference_id` int unsigned not null default 0, '
            . 'primary key (id), '
            . 'index (null_val)'
            . ') engine=' . $currentEngine
            , []);

        $query->execute();


        $query = $db->create('test')->setEngine('memory');
        $this->checkQuery($query, 'create table if not exists `t_test` () engine=memory', []);
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
            ->addEnum('fruits', Fruits::cases())
            ->addStdEnum('colors', ['blue','green','yellow','black\'n\'white']);
        
        $this->checkQuery($query, 
            'create table if not exists `t_sample` ('
                . '`id` int unsigned auto_increment, '
                . '`fruits` enum("apple","banana","peach"), '
                . '`colors` enum("blue","green","yellow","black\'n\'white"), '
                . 'primary key (id)'
                . ') engine=InnoDB',
            []);
        
        $query->execute();
        $this->assertTrue(true);
    }        

}