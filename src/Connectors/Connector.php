<?php

namespace Tivins\Database\Connectors;

use PDO;
use PDOException;
use Tivins\Database\Exceptions\ConnectionException;

/**
 * Abstract class used by all connectors for Database.
 *
 * The connector need to create a new PDO handler.
 */
abstract class Connector
{
    /**
     * Try the connector to get a valid PDO object.
     *
     * This function is called by the Database to get the PDO handler.
     *
     * @throws ConnectionException
     */
    public function connect(): PDO
    {
        try {
            return $this->createHandler();
        } catch (PDOException $pdoException) {
            throw new ConnectionException($pdoException->getMessage());
        }
    }

    /**
     * Return a valid PDO object.
     */
    abstract public function createHandler(): PDO;
}
