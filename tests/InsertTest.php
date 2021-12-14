<?php
namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ ConnectionException, DatabaseException };
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

        $db->dropTable('geom');
        $db->create('geom')
            ->addAutoIncrement('gid')
            ->addString('name')
            ->addGeometry('position')
            ->addGeometry('boundary')
            ->execute();

        $name = 'g_' . time();
        $x = 123;
        $y = 456;
        $coords = ["0 0", "0 1", "1 1", "1 0", "0 0"];
        $polygon = 'POLYGON(('.implode(',', $coords).'))';

        $query = $db
            ->insert('geom')
            ->fields([
                'name'     => $name,
                'position' => new InsertExpression('POINT(?,?)', $x, $y),
                'boundary' => new InsertExpression('ST_GeomFromText(?)', $polygon),
            ])
            ;
        $query->execute();

        $this->checkQuery($query,
            'insert into `t_geom` (`name`,`position`,`boundary`) values (?,POINT(?,?),ST_GeomFromText(?))',
            [$name, $x, $y, $polygon]
        );

    }
}