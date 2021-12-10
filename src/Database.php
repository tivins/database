<?php

namespace Tivins\Database;

use Exception;
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
     * @throws Connectors\ConnectionException
     */
    public function __construct(Connector $connector)
    {
        $this->handler = $connector->connect();
        $this->configureAttributes();
    }

    /**
     * Define a string prefix for all tables of the database.
     * For example, if a prefix is set to `test_`, then, the table name `user` will be turned into `test_user`.
     *
     * @param string $prefix
     * @return $this
     */
    public function setTablePrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Defines a callback sent before executing the query.
     *
     * Signature : ```php function(string $sql, array $parameters): void;```
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
     * @throws DatabaseException
     */
    public function query(string $query, array $parameters = []): ?Statement
    {
        if ($this->logCallback) {
            call_user_func($this->logCallback, $query, $parameters);
        }

        $sth = $this->handler->prepare($query);
        try {
            $sth->execute($parameters);
        }
        catch (PDOException $exception)
        {
            throw new DatabaseException();
        }
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
        try {
            return $this->select($tableName, 't')
                ->addFields('t')
                ->condition($column, $value)
                ->execute()
                ->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @throws DatabaseException
     */
    public function dropTable(string $tableName): self
    {
        $tableName = $this->prefixTableName($tableName);
        $this->query("drop table if exists `$tableName`");
        return $this;
    }

    /**
     * Destroy all records of the table $tableName and reset auto-increment index.
     *
     * @throws DatabaseException
     */
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
     * Alias of PDO::beginTransaction()
     * @throws PDOException
     */
    public function transaction()
    {
        $this->handler->beginTransaction();
    }

    /**
     * Alias of PDO::rollback()
     * @throws PDOException
     */
    public function rollback()
    {
        $this->handler->rollBack();
    }

    /**
     * Alias of PDO::commit()
     * @throws PDOException
     */
    public function commit()
    {
        $this->handler->commit();
    }
}
