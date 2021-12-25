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

    public function build(): QueryData
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

        $condSql = '';
        $queryData = $this->buildConditions();
        if (! $queryData->empty()) $condSql = " where $queryData->sql";
        $args = array_merge($args, $queryData->parameters);

        $sql = "update $this->tableName set $data$condSql";
        return new QueryData($sql, $args);
    }
}
