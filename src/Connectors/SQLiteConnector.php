<?php

namespace Tivins\Database\Connectors;

use PDO;

class SQLiteConnector extends Connector
{
    private $dsn;

    public function __construct(string $filename)
    {
        $this->dsn = 'sqlite:' . $filename;
    }

    public function createHandler(): PDO
    {
        return new PDO($this->dsn);
    }
}