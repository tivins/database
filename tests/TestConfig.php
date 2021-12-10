<?php

namespace Tivins\Database\Tests;

use Tivins\{Database\Connectors\ConnectionException, Database\Database, Database\Connectors\MySQLConnector};
use Exception;

/**
 * Static class used to get the database object during tests.
 */
class TestConfig
{
    private static Database $db;

    /**
     *
     * @throws ConnectionException
     */
    public static function db(): Database
    {
        if (isset(self::$db)) {
            return self::$db;
        }

        self::$db = new Database(
            new MySQLConnector(
                getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS'),
                getenv('DB_HOST'),
            )
        );
        self::$db->setTablePrefix('t_');
        self::initializeTables();
        return self::$db;
    }

    /**
     */
    public static function initializeTables(): void
    {
        self::$db->dropTable('users');

        self::$db->create('users')
            ->addAutoIncrement('uid', true)
            ->addString('name', nullable: false)
            ->addBool('state', 0)
            ->setUnique(['name'])
            ->execute();

    }
}