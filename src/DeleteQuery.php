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
        $qData = new QueryData("delete from `$this->tableName`");
        return $qData->merge($this->buildConditions(), ' where ');
    }
}