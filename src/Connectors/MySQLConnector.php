<?php

namespace Tivins\Database\Connectors;

use PDO;

class MySQLConnector extends Connector
{
    private $dsn;
    private $user;
    private $password;

    public function __construct(string $dbname, string $user, string $password, string $host = 'localhost')
    {
        $this->dsn = 'mysql:dbname=' . $dbname . ';host=' . $host;
        $this->user = $user;
        $this->password = $password;
    }

    public function createHandler(): PDO
    {
        return new PDO($this->dsn, $this->user, $this->password);
    }
}