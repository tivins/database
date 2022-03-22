<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Database;
use Tivins\Database\Connectors\SQLiteConnector;
use Tivins\Database\Exceptions\ConnectionException;

class SQLiteTest //extends TestCase
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
    public function testConnection()
    {
        $this->getDatabase();
        $this->assertFileExists('sqlite.db');
        unlink('sqlite.db');
    }

    /**
     * @throws ConnectionException
     */
    public function testShowTable()
    {
        $db = $this->getDatabase();
        $db->query('create table test(id)');
        $tables = $db->getTables();
        self::assertEquals(['test'], $tables);
    }
}