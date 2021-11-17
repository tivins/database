<?php

namespace Tivins\Database;

class SelectQuery extends Query
{
    private string $tableAlias;
    private array $fields = [];
    private array $expressions = [];
    private array $joins = [];
    private array $orders = [];
    private array $placeholders = [];
    private array $limits = [];
    private string $groupByExp;

    public function __construct(Database $db, string $tableName, string $tableAlias)
    {
        parent::__construct($db, $tableName);
        $this->tableAlias = $tableAlias;
    }

    /**
     *
     */
    public function addField(string $tableAlias, string $field, string $fieldAlias = '')
    {
        $this->fields[] = "{$tableAlias}.`{$field}`" . ($fieldAlias ? " as {$fieldAlias}" : '');
        return $this;
    }

    /**
     *
     */
    public function addFields(string $tableAlias, ?array $fields = null)
    {
        if (is_null($fields)) {
            $this->fields[] = "{$tableAlias}.*";
        } else {
            foreach ($fields as $field) {
                $this->fields[] = "{$tableAlias}.`{$field}`";
            }
        }
        return $this;
    }

    /**
     *
     */
    public function addExpression(string $expression, string $fieldAlias, array $values = [])
    {
        $this->expressions[] = ['sql' => "{$expression} as {$fieldAlias}", 'data' => $values];
        return $this;
    }

    /**
     *
     */
    public function leftJoin(string $tableName, string $tableAlias, string $condition)
    {
        $this->joins[] = "left join `{$tableName}` {$tableAlias} on {$condition}";
        return $this;
    }

    /**
     *
     */
    public function innerJoin(string $tableName, string $tableAlias, string $condition)
    {
        $this->joins[] = "inner join `{$tableName}` {$tableAlias} on {$condition}";
        return $this;
    }

    /**
     *
     */
    public function orderBy($field, $dir)
    {
        $this->orders[] = "$field $dir";
        return $this;
    }
    /**
     *
     */
    public function groupBy($exp)
    {
        $this->groupByExp = $exp;
        return $this;
    }

    /**
     *
     */
    public function limit($count)
    {
        $this->limits = [$count];
        return $this;
    }

    /**
     *
     */
    public function limitFrom($offset, $count)
    {
        $this->limits = [$offset, $count];
        return $this;
    }

    public function build(): array
    {
        $args = [];
        // fields
        $what = $this->fields;
        if (!empty($this->expressions)) {
            $what = array_merge($what, array_column($this->expressions, 'sql'));
            $args = array_merge($args, ...array_column($this->expressions, 'data'));
        }
        $what = implode(',', $what);

        $from = "{$this->tableName} {$this->tableAlias}";
        $joins = implode(' ', $this->joins);
        list($condSql, $condArgs) = $this->buildConditions();
        if (!empty($condSql)) $condSql = "where $condSql";
        $args = array_merge($args, $condArgs);
        $order = '';
        $group = '';
        if (!empty($this->orders)) {
            $order .= 'order by ' . implode(', ', $this->orders);
        }
        if (!empty($this->groupByExp)) {
            $group .= 'group by ' . $this->groupByExp;
        }
        $limits = '';
        if (!empty($this->limits)) {
            $limits = 'limit ' . join(',', $this->limits);
        }
        $sql  = trim("select {$what} from {$from} {$joins} {$condSql} {$group} {$order} {$limits}");
        return [$sql, $args];
    }
}
