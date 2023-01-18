<?php

namespace Tivins\Database;

use PDO;
use PDOException;
use Tivins\Database\Connectors\Connector;
use Tivins\Database\Connectors\ConnectorType;
use Tivins\Database\Exceptions\ConditionException;
use Tivins\Database\Exceptions\ConnectionException;
use Tivins\Database\Exceptions\DatabaseException;

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
     * @var Callable|null
     */
    private $failureCallback = null;

    private PDO $handler;

    /**
     * Initialize a new Database object from the given connector.
     * @throws ConnectionException
     */
    public function __construct(private readonly Connector $connector)
    {
        $this->handler = $connector->connect();
        $this->configureAttributes();
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
     * The prototype of the callback should be :
     *
     *      php function(string $sql, array $parameters): void;
     *
     * if $failureCallback is null, the callback feature is cancelled.
     */
    public function setLogCallback(?callable $callback): void
    {
        $this->logCallback = $callback;
    }

    /**
     * Define a failure callback called when an exception is thrown in the query() function.
     * The prototype of the callback should be :
     *
     *     function(Database $db, DatabaseException $exception): bool;
     *
     * Where the function's return TRUE to abort exception, or FALSE to keep unchanged the exception flow.
     *
     * if $failureCallback is null, the callback feature is cancelled.
     *
     * @param Callable|null $failureCallback
     * @return Database
     */
    public function setFailureCallback(?callable $failureCallback): Database
    {
        $this->failureCallback = $failureCallback;
        return $this;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @alias PDO::lastInsertId()
     */
    public function lastId(): int
    {
        return $this->handler->lastInsertId();
    }

    /**
     * Creates a select query for $tableName.
     */
    public function select(string $tableName, string $alias): SelectQuery
    {
        return new SelectQuery($this, $this->prefixTableName($tableName), $alias);
    }
    /**
     * Creates a MergeQuery for $tableName.
     */
    public function selectInsert(string $tableName): SelectInsertQuery
    {
        return new SelectInsertQuery($this, $tableName);
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
     * @throws DatabaseException|ConditionException
     * @example
     *      ```php
     *      $db_user = $db->fetchRow('users', 'uid', $userId);
     *      ```
     */
    public function fetchRow(string $tableName, string $column, $value): ?object
    {
        return $this->select($tableName, 't')
            ->addFields('t')
            ->condition($column, $value)
            ->execute()
            ->fetch();
    }

    /**
     * @throws DatabaseException
     */
    public function dropTable(string $tableName, bool $prefix = true): self
    {
        $tableName = $prefix ? $this->prefixTableName($tableName) : $tableName;
        $this->query("drop table if exists `$tableName`");
        return $this;
    }

    public function getTables(): array
    {
        return $this->query($this->connector->getShowTablesQuery())->fetchCol();
    }

    /**
     * @throws DatabaseException
     */
    public function dropAllTables(): array
    {
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $this->dropTable($table, prefix: false);
        }
        return $tables;
    }

    public function prefixTableName(string $tableName): string
    {
        return $this->prefix . $tableName;
    }

    /**
     * @throws DatabaseException
     */
    public function query(string $query, array $parameters = []): Statement
    {
        if ($this->logCallback) {
            call_user_func($this->logCallback, $query, $parameters);
        }
        $sth = $this->handler->prepare($query);
        try {
            $sth->execute($parameters);
        } catch (PDOException $exception) {
            $cancelException = false;
            if ($this->failureCallback && call_user_func($this->failureCallback, $this, $exception)) {
                $cancelException = true;
            }
            if (!$cancelException) {
                throw new DatabaseException($exception);
            }
        }
        return new Statement($sth);
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

    /**
     * Gets the columns used as primary key.
     *
     * @throws DatabaseException
     */
    public function getPrimary(string $tableName): array
    {
        $tableName = $this->prefixTableName($tableName);
        $keys = $this->query('SHOW KEYS FROM ' . $tableName . ' where key_name = \'PRIMARY\'')->fetchAll();
        return array_column($keys, 'Column_name');
    }

    /**
     * Alias of PDO::beginTransaction()
     * @throws PDOException
     */
    public function transaction(): void
    {
        $this->handler->beginTransaction();
    }

    /**
     * Alias of PDO::rollback()
     * @throws PDOException
     */
    public function rollback(): void
    {
        $this->handler->rollBack();
    }

    /**
     * Alias of PDO::commit()
     * @throws PDOException
     */
    public function commit(): void
    {
        $this->handler->commit();
    }

    /**
     * Registers a User Defined Function for use in SQL statements.
     */
    public function sqliteCreateFunction($function_name, $callback, $num_args = -1, $flags = 0): bool
    {
        return $this->handler->sqliteCreateFunction($function_name, $callback, $num_args, $flags);
    }

    public function getConnectorType(): ConnectorType
    {
        return $this->connector->getType();
    }
}
