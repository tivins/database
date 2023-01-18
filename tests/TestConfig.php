<?php

namespace Tivins\Database\Tests;

use Tivins\Database\{Database, Connectors\MySQLConnector};
use Tivins\Database\Exceptions\{ConnectionException, DatabaseException};
use function PHPUnit\Framework\assertEquals;

/**
 * Static class used to get the database object during tests.
 */
class TestConfig
{
    private static Database $db;

    /**
     *
     * @throws ConnectionException | DatabaseException
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
                3306
            )
        );
        self::$db->setTablePrefix('t_');
        self::initializeTables();
        return self::$db;
    }

    /**
     * @throws DatabaseException
     */
    public static function initializeTables(): void
    {
        try {
            self::$db->dropTable('users');
        } catch(DatabaseException $ex) {
        }

        self::$db->create('users')
            ->addAutoIncrement('uid', true)
            ->addString('name', nullable: false)
            ->addBool('state', 0)
            ->addUniqueKey(['name'])
            ->execute();

    }
}