<?php

namespace Tivins\Database\Connectors;

use PDO;

class SQLiteConnector implements Connector
{
    private $dsn;

    public function __construct(string $filename)
    {
        $this->dsn = 'sqlite:' . $filename;
    }

    public function connect(): PDO
    {
        return new PDO($this->dsn);
    }
}