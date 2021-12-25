<?php

namespace Tivins\Database;

/**
 *
 */
class InsertQuery extends Query
{
    /**
     * @var array[]
     */
    private array  $fields    = [];
    private ?array $fixedKeys = null;

    /**
     * @param array<string, string> $data
     * @return $this The current object.
     */
    public function fields(array $data): self
    {
        $this->fields = [$data];
        return $this;
    }

    /**
     * @param array<string,string>[] $data
     * @param array|null $keys The fixed keys, in case of $data is not key-indexed
     * @return $this The current object.
     * @see $fixedKeys
     */
    public function multipleFields(array $data, ?array $keys = null): self
    {
        $this->fields    = $data;
        $this->fixedKeys = $keys;
        return $this;
    }

    public function build(): QueryData
    {
        $keys             = [];
        $values           = [];
        $params           = [];
        $valuesStatements = [];

        foreach ($this->fields as $fields) // foreach groups
        {
            foreach ($fields as $key => $value) // foreach dataset
            {
                $keys[$key] = "`$key`";
                if ($value instanceof InsertExpression) // expression
                {
                    $values[] = $value->getExpression();
                    $params   = array_merge($params, $value->getParameters());
                } else {
                    $values[] = '?';
                    $params[] = $value;
                }
            }
            $valuesStatements[] = '(' . implode(',', $values) . ')';
            $values             = [];
        }
        if (!empty($this->fixedKeys)) {
            $keys = $this->fixedKeys;
        }
        $sql = sprintf("insert into `%s` (%s) values %s", $this->tableName, implode(',', $keys), implode(',', $valuesStatements));
        return new QueryData($sql, $params);
    }
}