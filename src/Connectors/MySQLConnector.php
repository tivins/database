<?php

namespace Tivins\Database\Connectors;

use PDO;

class MySQLConnector extends Connector
{
    protected string $connectorType = 'mysql';

    private $dsn;
    private $user;
    private $password;

    public function __construct(string $dbname, string $user, string $password, ?string $host = null, ?int $port = null)
    {
        $this->dsn = 'mysql:dbname=' . $dbname
            . ($host ? ';host=' . $host : '')
            . ($port ? ';port=' . $port : '');
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function createHandler(): PDO
    {
        return new PDO($this->dsn, $this->user, $this->password);
    }

    /**
     * @inheritDoc
     */
    public function getShowTablesQuery(): string
    {
        return 'show tables';
    }

}
