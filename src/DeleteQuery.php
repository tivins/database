<?php

namespace Tivins\Database;

/**
 * Prepare and perform a `delete` SQL-query.
 *
 * ```php
 * $db->delete('users')
 *    ->whereIn('id', [3, 4, 5])
 *    ->execute();
 * ```
 */
class DeleteQuery extends Query
{
    public function build(): QueryData
    {
        $queryData = $this->buildConditions();
        if (! $queryData->empty()) {
            $queryData->sql = 'where ' . $queryData->sql;
        }
        $queryData->sql = "delete from `$this->tableName` $queryData->sql";
        return $queryData;
    }
}