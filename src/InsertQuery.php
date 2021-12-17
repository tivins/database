<?php

namespace Tivins\Database;

/**
 *
 */
class InsertQuery extends Query
{
    private array $fields = [];

    public function fields(array $data): self
    {
        $this->fields = $data;
        return $this;
    }

    public function build(): array
    {
        $keys   = [];
        $values = [];
        $params = [];

        foreach ($this->fields as $key => $value)
        {
            $keys[] = "`$key`";
            if ($value instanceof InsertExpression) // expression
            {
                $values[] = $value->getExpression();
                $params = array_merge($params, $value->getParameters());
            }
            else
            {
                $values[] = '?';
                $params[] = $value;
            }
        }
        $sql = sprintf("insert into `%s` (%s) values (%s)", $this->tableName, implode(',', $keys), implode(',', $values));
        return [$sql, $params];
    }
}