<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Database;
use Tivins\Database\Connectors\SQLiteConnector;
use Tivins\Database\Exceptions\ConnectionException;

class SQLiteTest extends TestCase
{
    /**
     * @throws ConnectionException
     */
    private function getDatabase(): Database
    {
        $connector = new SQLiteConnector('sqlite.db');
        return new Database($connector);
    }

    /**
     * @throws ConnectionException
     */
    public function testConnection(): void
    {
        $this->getDatabase();
        $this->assertFileExists('sqlite.db');
        unlink('sqlite.db');
    }

    /**
     * @throws ConnectionException
     */
    public function testShowTable(): void
    {
        $db = $this->getDatabase();
        $db->dropTable('test');
        $db->query('create table test(id)');
        $tables = $db->getTables();
        self::assertEquals(['test'], $tables);

        $db->sqliteCreateFunction('addOne', function($a) {
            return $a + 1;
        }, 1);

        $db->insert('test')->fields(['id'=>1])->execute();

        $idPlusOne = $db->select('test', 't')
            ->addExpression('addOne(t.id)', 'id_1')
            ->condition('t.id', 1)
            ->execute()
            ->fetchField();
        assert(2, $idPlusOne);
    }
}