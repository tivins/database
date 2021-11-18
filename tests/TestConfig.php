<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Database;
use Tivins\Database\Connectors\MySQLConnector;

/**
 * Static class used to get the database object during tests.
 */
class TestConfig
{
    private static Database $db;

    public static function db() : Database
    {
        if (! isset(self::$db))
        {
            self::$db = new Database(
                new MySQLConnector(
                    getenv('DBNAME'),
                    getenv('DBUSER'),
                    getenv('DBPASS'),
                    getenv('DBHOST'),
                )
            );
        }
        return self::$db;
    }
}