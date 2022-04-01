<?php

namespace Tivins\Database\Map;

use Tivins\Database\Database;

class DBOManager
{
    private static Database $database;

    /**
     * @return Database
     */
    public static function db(): Database
    {
        return self::$database;
    }

    /**
     * @param Database $database
     */
    public static function setDatabase(Database $database): void
    {
        self::$database = $database;
    }
}

