<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Conditions;
use Tivins\Database\Exceptions\ConditionException;
use Tivins\Database\Exceptions\ConnectionException;

/**
 * This test is a pseudo real-life request.
 *
 * It's not designed to be efficient, but, to test most code of this project.
 */
class FullTest extends TestBase
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
        $this->selectWhereInOrder();
        $this->selectIndex();
    }

    public function testOperators()
    {
        $db = TestConfig::db();
        $operators = Conditions::getOperators();
        self::assertContains('=', $operators);
        Conditions::setOperators([]);
        self::assertEmpty(Conditions::getOperators());
        Conditions::setOperators($operators);
    }

    public function loadLibrary(string $filename): array
    {
        $books = [];
        $fp    = fopen($filename, mode: 'r');
        while ($row = fgetcsv($fp)) {
            if (empty(array_filter($row))) {
                continue;
            }
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
        $this->createBooks($this->books);
    }

    /**
     * @throws ConditionException
     * @throws ConnectionException
     */
    public function createBooks(array $books)
    {
        $data = [];
        foreach ($books as $book) {
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


    //----------------------


    /**
     * @throws ConnectionException
     */
    public function selectGeneral()
    {
        $posts = TestConfig::db()->select('books', 'b')
            ->leftJoin('authors', 'a', 'b.id_author = a.id')
            ->addFields('b')
            ->addField('a', 'name', 'author_name')
            ->limit(2)
            ->execute()
            ->fetchAll();

        $expected = [
            (object)['id' => 1, 'title' => 'Les Misérables', 'id_author' => 1, 'year' => 1862, 'author_name' => 'Victor Hugo'],
            (object)['id' => 2, 'title' => 'The Time Machine', 'id_author' => 2, 'year' => 1895, 'author_name' => 'H.G. Wells'],
        ];
        $this->assertEquals($expected, $posts);
    }

    /**
     * @depends testFull
     * @throws ConnectionException
     */
    public function selectWhereInOrder()
    {
        $books = TestConfig::db()->select('books', 'b')
            ->leftJoin('authors', 'a', 'b.id_author = a.id')
            ->addField('a', 'id')
            ->whereIn('b.year', [1808, 1864, 1862, 1837])
            ->limit(2)
            ->execute()
            ->fetchCol();
        $this->assertEquals([1, 3], $books);


        $books = TestConfig::db()->select('books', 'b')
            ->leftJoin('authors', 'a', 'b.id_author = a.id')
            ->addField('a', 'id')
            ->like('a.name', '%Jules%')
            ->orderBy('b.year', 'asc')
            ->limit(1)
            ->execute()
            ->fetchField();
        $this->assertEquals(6, $books);
    }

    /**
     * @throws ConnectionException
     */
    private function selectIndex()
    {
        $books = TestConfig::db()->select('books', 'b')
            ->addExpression('substring(b.title,1,1)', 'firstLetter')
            ->addCount('b.id', 'num')
            ->groupBy('firstLetter')
            ->orderBy('firstLetter', 'asc')
            ->execute()
            ->fetchAssocKey('firstLetter','num');

        $this->assertIdentical([
            '1' => 1,
            'D' => 1,
            'F' => 3,
            'G' => 2,
            'H' => 2,
            'L' => 3,
            'M' => 2,
            'O' => 1,
            'P' => 1,
            'T' => 5,
            'V' => 1,
        ], $books);
    }
}