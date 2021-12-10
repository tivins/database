<?php

namespace Tivins\Database\Tests;

use Tivins\{Database\CreateQuery, Database\Database, Database\Connectors\MySQLConnector};

/**
 * Static class used to get the database object during tests.
 */
class TestConfig
{
    private static Database $db;

    public static function db() : Database
    {
        if (isset(self::$db))
        {
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

        self::$db->dropTable('users');

        self::$db->create('users')
            ->addAutoIncrement('uid', true)
            ->addString('name', nullable: false)
            ->addBool('state', 0)
            ->setUnique(['name'])
            ->execute();

        return self::$db;
    }
}