<?php

namespace Tivins\Database\Connectors;

use Exception;
use PDO;

/**
 * This connector allows to use an existing database connection.
 * This connector is useful when you are using multiple database connection systems.
 */
class NativeConnector extends Connector
{
    public function __construct(private readonly PDO $pdo, ConnectorType $type)
    {
        $this->connectorType = $type;
    }

    /**
     * @inheritDoc
     */
    public function createHandler(): PDO
    {
        return $this->pdo;
    }
}