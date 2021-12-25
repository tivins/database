<?php
namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};
use Tivins\Database\InsertExpression;

class InsertTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     * @throws ConditionException
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
    public function testInsertMultiples()
    {
        $db = TestConfig::db();

        $db->dropTable('book')
            ->create('book')
            ->addAutoIncrement('id')
            ->addText('title')
            ->addText('author')
            ->execute();

        $db->insert('book')
            ->fields([
                'title' => 'Book title',
                'author' => 'John Doe',
            ])
            ->execute();

        $books = $db->select('book','b')
            ->addFields('b')
            ->execute()
            ->fetchAll();

        $expected = [
            ['id' => 1, 'title' => 'Book title', 'author' => 'John Doe'],
        ];

        $this->assertEquals(json_encode($expected), json_encode($books));

        $db->truncateTable('book');

        $expected = [
            ['title' => 'title1', 'author' => 'author1'],
            ['title' => 'title2', 'author' => 'author2'],
            ['title' => 'title3', 'author' => 'author3'],
        ];

        $db->insert('book')
            ->multipleFields($expected)
            ->execute();

        $books = $db->select('book','b')
            ->addFields('b')
            ->execute()
            ->fetchAll();

        $expectedList = [
            ['id' => 1, 'title' => 'title1', 'author' => 'author1'],
            ['id' => 2, 'title' => 'title2', 'author' => 'author2'],
            ['id' => 3, 'title' => 'title3', 'author' => 'author3'],
        ];
        $this->assertEquals(json_encode($expectedList), json_encode($books));
    }

    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testInsertMultiplesFixed()
    {
        $db = TestConfig::db();

        $db->dropTable('book')
            ->create('book')
            ->addAutoIncrement('id')
            ->addText('title')
            ->addText('author')
            ->execute();

        $keys = ['title', 'author'];
        $expected = [
            ['title1', 'author1'],
            ['title2', 'author2'],
            ['title3', 'author3'],
        ];

        $db->insert('book')
            ->multipleFields($expected, $keys)
            ->execute();

        $books = $db->select('book','b')
            ->addFields('b')
            ->execute()
            ->fetchAll();

        $expectedList = [
            ['id' => 1, 'title' => 'title1', 'author' => 'author1'],
            ['id' => 2, 'title' => 'title2', 'author' => 'author2'],
            ['id' => 3, 'title' => 'title3', 'author' => 'author3'],
        ];
        $this->assertEquals(json_encode($expectedList), json_encode($books));
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