<?php
namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ ConnectionException, DatabaseException };
use Tivins\Database\Database;
use Tivins\Database\InsertExpression;

class InsertTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testInsert()
    {
        $db = TestConfig::db();

        $db->truncateTable('users');

        $query = $db->insert('users')
            ->fields(['name' => 'test'])
            ;
        $this->checkQuery($query,
            'insert into `t_users` (`name`) values (?)', ['test']);

        $query->execute();
        $this->assertEquals(1, TestConfig::db()->lastId());

        $object = $db->fetchRow('users', 'uid', 1);
        $this->assertIsObject($object);

        $object = $db->fetchRow('users', 'uid', 0);
        $this->assertNull($object);
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testInsertExpression()
    {
        $db = TestConfig::db();

        $db->create('geom')
            ->addAutoIncrement('gid')
            ->addString('name', 255, nullable: false)
            ->addGeometry('position')
            ->execute();

        $name = 'g_' . time();
        $x = 123;
        $y = 456;

        $query = $db
            ->insert('geom')
            ->fields([
                'name'     => $name,
                'position' => new InsertExpression('POINT(?,?)', $x, $y)
            ])
            ;
        $query->execute();

        $this->checkQuery($query,
            'insert into `t_geom` (`name`,`position`) values (?,POINT(?,?))',
            [$name, $x, $y]
        );

    }
}