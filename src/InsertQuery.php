<?php

namespace Tivins\Database;

/**
 *
 */
class InsertQuery extends Query
{
    private array $fields;

    public function fields(array $data): self
    {
        $this->fields = $data;
        return $this;
    }

    public function build(): array
    {
        $keys = '`' . implode('`,`', array_keys($this->fields)) . '`';

        $placeholders = implode(',', array_fill(0, count($this->fields), '?'));

        $sql = "insert into `$this->tableName` ($keys) values ($placeholders)";

        return [$sql, array_values($this->fields)];
    }
}