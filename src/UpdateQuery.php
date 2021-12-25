<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\DatabaseException;

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
        $qData = new QueryData("update $this->tableName");
        $qData->merge($this->buildUpdate(), ' set ');
        $qData->merge($this->buildConditions(), ' where ');
        return $qData;
    }

    /**
     * @return QueryData
     */
    public function buildUpdate(): QueryData
    {
        $args = [];
        $data = [];
        foreach ($this->fields as $key => $value) {
            if (is_numeric($key)) {
                $data[] = $value;
            }
            elseif ($value instanceof InsertExpression) {
                $data[] = "`$key`={$value->getExpression()}";
                $args   = array_merge($args, $value->getParameters());
            }
            else {
                $data[] = "`$key`=?";
                $args[] = $value;
            }
        }
        $data = implode(',', $data);
        return new QueryData($data, $args);
    }
}
