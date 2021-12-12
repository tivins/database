<?php

namespace Tivins\Database;

class UpdateQuery extends Query
{
    protected array $fields;

    public function fields(array $data): UpdateQuery
    {
        $this->fields = $data;
        return $this;
    }

    public function build(): array
    {
        $args = [];
        $data = [];
        foreach ($this->fields as $key => $value) {
            if (is_numeric($key)) {
                $data[] = $value;
            }
            elseif ($value instanceof InsertExpression)
            {
                $data[] = "`$key`={$value->getExpression()}";
                $args = array_merge($args, $value->getParameters());
            }
            else {
                $data[] = "`$key`=?";
                $args[] = $value;
            }
        }
        $data = implode(',', $data);

        [$condSql, $condArgs] = $this->buildConditions();
        if (!empty($condSql)) $condSql = " where $condSql";
        $args = array_merge($args, $condArgs);

        $sql = "update $this->tableName set $data$condSql";
        return [$sql, $args];
    }
}
