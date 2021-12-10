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
    public function build(): array
    {
        [$condSql, $params] = $this->buildConditions();
        if (!empty($condSql)) $condSql = "where $condSql";
        $sql = "delete from `$this->tableName` $condSql";
        return [$sql, $params];
    }
}