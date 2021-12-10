<?php
namespace Tivins\Database\Tests;

class InsertTest extends TestBase
{
    public function testInsert()
    {
        TestConfig::db()->truncateTable('users');

        $query = TestConfig::db()
            ->insert('users')
            ->fields(['name' => 'test'])
            ;
        $this->checkQuery($query,
            'insert into `t_users` (`name`) values (?)', ['test']);

        $query->execute();
        $this->assertEquals(1, TestConfig::db()->lastId());

        $object = TestConfig::db()->fetchRow('users', 'uid', 1);
        $this->assertIsObject($object);

        $object = TestConfig::db()->fetchRow('users', 'uid', 0);
        $this->assertNull($object);

    }
}