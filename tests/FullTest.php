<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Exceptions\ConditionException;
use Tivins\Database\Exceptions\ConnectionException;

/**
 * This test is a pseudo real-life request.
 *
 * It's not designed to be efficient, but, to test most code of this project.
 */
class FullTest extends TestCase
{
    private array $books = [];

    /**
     * @throws ConnectionException
     * @throws ConditionException
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
            ->addInteger('year', null)
            ->execute();

        $this->books = $this->loadLibrary(__dir__ . '/data/books.cvs');
        $this->insertLibrary();

        $this->selectGeneral();
    }

    /**
     * @depends
     * @throws ConnectionException
     */
    public function selectGeneral()
    {
        $db = TestConfig::db();

        $posts = $db->select('books', 'b')
            ->leftJoin('authors', 'a', 'b.id_author = a.id')
            ->addFields('b')
            ->addField('a', 'name', 'author_name')
            ->limit(2)
            ->execute()
            ->fetchAll();

        $expected = [
            (object) [
                'id' => 1,
                'title' => 'Les Misérables',
                'id_author' => 1,
                'year' => 1862,
                'author_name' => 'Victor Hugo',
            ],
            (object) [
                'id' => 2,
                'title' => 'The Time Machine',
                'id_author' => 2,
                'year' => 1895,
                'author_name' => 'H.G. Wells',
            ],
        ];

        $this->assertEquals($expected, $posts);
    }

    /**
     */
    public function loadLibrary(string $filename): array
    {
        $books = [];
        $fp    = fopen($filename, mode: 'r');
        while ($row = fgetcsv($fp)) {
            if (!isset($header)) {
                $header = $row;
                continue;
            }
            $books[] = array_combine($header, $row);
        }
        return $books;
    }

    /**
     * @throws ConnectionException
     * @throws ConditionException
     */
    public function insertLibrary()
    {

        $this->createAuthors(array_column($this->books, 'author'));

        $data = [];
        foreach ($this->books as $book) {
            $author = $this->getAuthorByName($book['author']);
            $data[] = [
                'title'     => $book['title'],
                'id_author' => (int) ($author?->id),
                'year'      => $book['year'],
            ];
        }

        $db = TestConfig::db();
        $db->insert('books')
            ->multipleFields($data)
            ->execute();
    }

    /**
     * @param string[] $names
     * @throws ConnectionException
     */
    public function createAuthors(array $names)
    {
        $fields = array_map(fn(string $str) => [$str], $names);
        TestConfig::db()->insert('authors')
            ->multipleFields($fields, ['name'])
            ->execute();
    }

    /**
     * @throws ConnectionException
     * @throws ConditionException
     */
    public function getAuthorByName($name): object|null
    {
        return TestConfig::db()
            ->select('authors', 'a')
            ->addField('a', 'id')
            ->condition('a.name', $name)
            ->execute()
            ->fetch();
    }

}