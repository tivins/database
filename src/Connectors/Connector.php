<?php

namespace Tivins\Database\Connectors;

use PDO;
use PDOException;
use Tivins\Database\Exceptions\ConnectionException;

abstract class Connector
{
    /**
     * @throws ConnectionException
     */
    public function connect(): PDO
    {
        try {
            return $this->createHandler();
        } catch (PDOException $pdoException) {
            throw new ConnectionException();
        }
    }

    abstract public function createHandler(): PDO;
}
