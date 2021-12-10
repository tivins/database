<?php

namespace Tivins\Database;

use PDO;
use PDOException;
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

    /**
     *
     */
    private PDO $handler;

    /**
     * Initialize a new Database object from the given connector.
     */
    public function __construct(Connector $connector)
    {
        $this->handler = $connector->connect();
        $this->configureAttributes();
    }

    public function setTablePrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
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
    private function configureAttributes(): void
    {
        $this->handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @throws PDOException
     */
    public function query(string $query, array $parameters = []): Statement
    {
        if ($this->logCallback) {
            call_user_func($this->logCallback, $query, $parameters);
        }


        $sth = $this->handler->prepare($query);
        $sth->execute($parameters);
        return new Statement($sth);
    }

    /**
     *
     */
    public function lastId(): int
    {
        return $this->handler->lastInsertId();
    }

    /*
        Select, Insert, Update, Merge, Delete
    */

    /**
     * Creates a MergeQuery for $tableName.
     */
    public function select(string $tableName, string $alias): SelectQuery
    {
        return new SelectQuery($this, $this->prefix . $tableName, $alias);
    }

    /**
     * Creates a MergeQuery for $tableName.
     */
    public function merge(string $tableName): MergeQuery
    {
        return new MergeQuery($this, $this->prefix . $tableName);
    }

    /**
     * Creates an InsertQuery for $tableName.
     */
    public function insert(string $tableName): InsertQuery
    {
        return new InsertQuery($this, $this->prefix . $tableName);
    }

    /**
     * Creates an UpdateQuery for $tableName.
     */
    public function update(string $tableName): UpdateQuery
    {
        return new UpdateQuery($this, $this->prefix . $tableName);
    }

    /**
     * Creates a DeleteQuery for $tableName.
     */
    public function delete(string $tableName): DeleteQuery
    {
        return new DeleteQuery($this, $this->prefix . $tableName);
    }

    /**
     * Creates a CreateQuery for $tableName.
     */
    public function create(string $tableName): CreateQuery
    {
        return new CreateQuery($this, $this->prefix . $tableName);
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
     * Shortcut to get a single row from the given table, column, value.
     *
     * @example
     *
     * ```php
     * $db_user = $db->fetchRow('users', 'uid', $userId);
     * ```
     */
    public function fetchRow(string $tableName, string $column, $value): ?object
    {
        return $this->select($tableName,'t')
                    ->addFields('t')
                    ->condition($column, $value)
                    ->execute()
                    ->fetch();
    }

    public function dropTable(string $tableName): self
    {
        $tableName = $this->prefixTableName($tableName);
        $this->query("drop table if exists `$tableName`");
        return $this;
    }

    public function truncateTable(string $tableName): self
    {
        $tableName = $this->prefixTableName($tableName);
        $this->query("truncate table `$tableName`");
        return $this;
    }

    public function prefixTableName(string $tableName): string
    {
        return $this->prefix . $tableName;
    }

    /**
     * Wrappers
     */
    public function transaction()
    {
        $this->handler->beginTransaction();
    }
    public function rollback()
    {
        $this->handler->rollBack();
    }
    public function commit()
    {
        $this->handler->commit();
    }
}
