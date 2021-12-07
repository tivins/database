<?php

namespace Tivins\Database;


class Query extends Conditions
{
    protected Database $db;
    protected string $tableName;

    public function __construct(Database $db, string $tableName)
    {
        parent::__construct();
        $this->db  = $db;
        $this->tableName  = $tableName;
    }

    public function build(): array
    {
        return [];
    }

    public function execute(): Statement
    {
        return $this->db->query(...$this->build());
    }
}