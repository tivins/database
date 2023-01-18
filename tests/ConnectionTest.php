<?php

namespace Tivins\Database\Tests;

use PHPUnit\Framework\TestCase;
use Tivins\Database\Connectors\ConnectorType;
use Tivins\Database\Connectors\MySQLConnector;
use Tivins\Database\Database;
use Tivins\Database\Exceptions\ConnectionException;
use function PHPUnit\Framework\assertEquals;

class ConnectionTest extends TestCase
{
    /**
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

    /**
     * @throws ConnectionException
     */
    public function testConnector()
    {
        $db = TestConfig::db();
        $this->assertEquals(ConnectorType::MYSQL, $db->getConnectorType());
    }

}