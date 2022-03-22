<?php

namespace Tivins\Database\Connectors;

use PDO;

class SQLiteConnector extends Connector
{
    protected string $connectorType = 'sqlite';

    private $dsn;

    public function __construct(string $filename)
    {
        $this->dsn = 'sqlite:' . $filename;
    }

    public function createHandler(): PDO
    {
        return new PDO($this->dsn);
    }

    /**
     * @inheritDoc
     */
    public function getShowTablesQuery(): string
    {
        return 'SELECT name FROM sqlite_schema WHERE type =\'table\' AND name NOT LIKE \'sqlite_%\'';
    }
}
