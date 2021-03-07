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
            else {
                $data[] = "`{$key}`=?";
                $args[] = $value;
            }
        }
        $data = implode(',', $data);

        list($condSql, $condArgs) = $this->buildConditions();
        $args = array_merge($args, $condArgs);

        $sql = "update {$this->tableName} set {$data} {$condSql}";
        return [$sql, $args];
    }
}
