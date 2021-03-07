<?php

namespace Tivins\Database;

/**
 *
 */
class InsertQuery extends Query
{
    private array $fields;

    public function fields(array $data)
    {
        $this->fields = $data;
        return $this;
    }

    public function build(): array
    {
        $keys = '`' . implode('`,`', array_keys($this->fields)) . '`';

        $placehoders = implode(',', array_fill(0, count($this->fields), '?'));

        $sql = "insert into `{$this->tableName}` ({$keys}) values ({$placehoders})";

        return [$sql, array_values($this->fields)];
    }
}