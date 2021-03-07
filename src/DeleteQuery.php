<?php

namespace Tivins\Database;

/**
 * <code>
 * $db->delete('users')
 *    ->whereIn('id', [3, 4, 5])
 *    ->execute();
 * </code>
 */
class DeleteQuery extends Query
{
    public function build(): array
    {
        list($condSql, $params) = $this->buildConditions();
        $sql = "delete from `{$this->tableName}` {$condSql}";
        return [$sql, $params];
    }
}