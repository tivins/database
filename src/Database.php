<?php

namespace Tivins\Database;

use PDO;
use Tivins\Database\Connectors\Connector;

/**
 *
 */
class Database
{
    /**
     *
     */
    private string $prefix = '';

    /**
     * @var Callable|null
     */
    private $logCallback = null;
    private PDO $dbhandler;

    /**
     *
     */
    public function __construct(Connector $connector)
    {
        $this->dbhandler = $connector->connect();
        $this->configureAttributes();
    }

    /**
     * Defines a callback sent before executing the query.
     *
     * Signature : function(string $sql, array $parameters): void;
     */
    public function setLogCallback(Callable $callback): void
    {
        $this->logCallback = $callback;
    }

    /**
     *
     */
    private function configureAttributes()
    {
        $this->dbhandler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->dbhandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     *
     */
    public function query(string $query, array $parameters = []): Statement
    {
        if ($this->logCallback) {
            call_user_func($this->logCallback, $query, $parameters);
        }


        $sth = $this->dbhandler->prepare($query);
        $sth->execute($parameters);
        return new Statement($sth);
    }

    /**
     *
     */
    public function lastId(): int
    {
        return $this->dbhandler->lastInsertId();
    }

    /*
        Select, Insert, Update, Merge, Delete
    */

    /**
     * Creates a MergeQuery for $tableName.
     */
    public function select(string $tableName, string $alias): SelectQuery
    {
        return new SelectQuery($this, $tableName, $alias);
    }

    /**
     * Creates a MergeQuery for $tableName.
     */
    public function merge(string $tableName): MergeQuery
    {
        return new MergeQuery($this, $tableName);
    }

    /**
     * Creates an InsertQuery for $tableName.
     */
    public function insert(string $tableName): InsertQuery
    {
        return new InsertQuery($this, $tableName);
    }

    /**
     * Creates an UpdateQuery for $tableName.
     */
    public function update(string $tableName): UpdateQuery
    {
        return new UpdateQuery($this, $tableName);
    }

    /**
     * Creates a DeleteQuery for $tableName.
     */
    public function delete(string $tableName): DeleteQuery
    {
        return new DeleteQuery($this, $tableName);
    }

    /**
     *
     */
    public function or(): Conditions
    {
        return new Conditions(Conditions::MODE_OR);
    }
    /**
     *
     */
    public function and(): Conditions
    {
        return new Conditions(Conditions::MODE_AND);
    }

    /**
     *
     */
    public function fetchRow(string $tableName, string $column, $value): ?object
    {
        return $this->select($tableName,'t')
                    ->addFields('t')
                    ->condition($column, $value)
                    ->execute()
                    ->fetch();
    }

    /**
     * Wrappers
     */
    public function transaction()
    {
        $this->dbhandler?->beginTransaction();
    }
    public function rollback()
    {
        $this->dbhandler?->rollBack();
    }
    public function commit()
    {
        $this->dbhandler?->commit();
    }
}
