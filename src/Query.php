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
        return $this->executeQueryData($this->build());
    }

    /**
     * @throws DatabaseException
     */
    public function executeQueryData(QueryData $queryData): Statement
    {
        return $this->db->query($queryData->sql, $queryData->parameters);
    }

    /**
     * Create the SQL query string and the parameters.
     *
     * @return QueryData
     */
    abstract public function build(): QueryData;
}
