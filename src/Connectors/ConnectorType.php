<?php

namespace Tivins\Database\Connectors;

enum ConnectorType
{
    case NONE;
    case MYSQL;
    case SQLITE;

    /**
     * @throws \Exception
     */
    public function getShowTablesQuery(): string
    {
        return match ($this) {
            self::SQLITE => 'SELECT name FROM sqlite_master WHERE type =\'table\' AND name NOT LIKE \'sqlite_%\'',
            self::MYSQL => 'show tables',
            self::NONE => throw new \Exception('To be implemented'),
        };
    }
}