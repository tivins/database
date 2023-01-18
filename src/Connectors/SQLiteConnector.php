<?php

namespace Tivins\Database\Connectors;

use PDO;

class SQLiteConnector extends Connector
{
    private $dsn;

    public function __construct(string $filename)
    {
        $this->connectorType = ConnectorType::MYSQL;
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
        return 'SELECT name FROM sqlite_master WHERE type =\'table\' AND name NOT LIKE \'sqlite_%\'';
    }
}
