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
    public function testConnection()
    {
        $connector = new SQLiteConnector('sqlite.db');
        new Database($connector);
        $this->assertFileExists('sqlite.db');
        unlink('sqlite.db');
    }
}