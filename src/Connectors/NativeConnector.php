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
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @inheritDoc
     */
    public function createHandler(): PDO
    {
        return $this->pdo;
    }

    /**
     * @inheritDoc
     * @throws Exception Because we don't know the underlying engine system.
     * @todo Find way to do this.
     */
    public function getShowTablesQuery(): string
    {
        throw new Exception('Not implemented');
    }
}