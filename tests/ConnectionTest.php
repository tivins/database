<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Connectors\MySQLConnector;
use Tivins\Database\Database;
use Tivins\Database\Exceptions\ConnectionException;

class ConnectionTest extends TestCase
{
    /**
     * @throws ConnectionException
     */
    public function testFail()
    {
        $this->expectException(ConnectionException::class);
        new Database(
            new MySQLConnector(
                dbname:   'wrong',
                user:     'wrong',
                password: 'wrong',
                host:     'wrong',
            )
        );
    }
}