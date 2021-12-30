<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Exceptions\ConnectionException;

class FullTest extends TestCase
{
    /**
     * @throws ConnectionException
     */
    public function testFull()
    {
        $db = TestConfig::db();
        $db->dropTable('authors');
        $db->dropTable('books');

        $db->create('authors')
            ->addAutoIncrement('id')
            ->addString('name')
            ->execute();

        $db->create('books')
            ->addAutoIncrement('id')
            ->addString('title')
            ->addPointer('id_author')
            ->execute();

        $db->insert('authors')
            ->multipleFields([['author1'],['author2']],['name'])
            ->execute();
        $db->insert('books')
            ->fields(['title' => 'book1', 'id_author' => 1])
            ->execute();
        $db->insert('books')
            ->fields(['title' => 'book2', 'id_author' => 2])
            ->execute();

        $posts = $db->select('books', 'b')
            ->leftJoin('authors', 'a', 'b.id_author = a.id')
            ->addFields('b')
            ->addField('a', 'name', 'author_name')
            ->execute()
            ->fetchAll();

        $expected = [
            (object) [
                'id' => 1,
                'title' => 'book1',
                'id_author' => 1,
                'author_name' => 'author1',
            ],
            (object) [
                'id' => 2,
                'title' => 'book2',
                'id_author' => 2,
                'author_name' => 'author2',
            ],
        ];
        $this->assertEquals($expected, $posts);
    }
}