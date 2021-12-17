<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\DatabaseException;

abstract class Query extends Conditions
{
    protected Database $db;
    protected string $tableName;

    /**
     * @param Database $db The database to query.
     * @param string $tableName The table name to select.
     */
    public function __construct(Database $db, string $tableName)
    {
        parent::__construct();
        $this->db = $db;
        $this->tableName = $tableName;
    }

    /**
     * @throws DatabaseException
     */
    public function execute(): Statement
    {
        return $this->db->query(...$this->build());
    }

    /**
     * @return array
     */
    abstract public function build(): array;
}