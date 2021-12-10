<?php

namespace Tivins\Database\Connectors;

use PDO;
use PDOException;

abstract class Connector
{
    abstract public function createHandler(): PDO;
    /**
     * @throws ConnectionException
     */
    public function connect(): PDO
    {
        try {
            return $this->createHandler();
        }
        catch (PDOException $pdoException)
        {
            throw new ConnectionException();
        }
    }
}
