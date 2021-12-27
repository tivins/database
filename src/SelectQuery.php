<?php

namespace Tivins\Database;

use Tivins\Database\Tests\SelectTest;

class SelectQuery extends Query
{
    private string $tableAlias;
    private array $fields = [];
    private array $expressions = [];
    private array $joins = [];
    private array $orders = [];
    private array $limits = [];
    private ?Conditions $having = null;
    private string $groupByExp;

    public function __construct(Database $db, string $tableName, string $tableAlias)
    {
        parent::__construct($db, $tableName);
        $this->tableAlias = $tableAlias;
    }

    /**
     *
     */
    public function addField(string $tableAlias, string $field, string $fieldAlias = ''): self
    {
        $this->fields[] = "$tableAlias.`$field`" . ($fieldAlias ? " as $fieldAlias" : '');
        return $this;
    }

    /**
     *
     */
    public function addFields(string $tableAlias, ?array $fields = null): self
    {
        if (is_null($fields)) {
            $this->fields[] = "$tableAlias.*";
        } else {
            foreach ($fields as $field) {
                $this->fields[] = "$tableAlias.`$field`";
            }
        }
        return $this;
    }

    /**
     *
     */
    public function addExpression(string $expression, string $fieldAlias, array $values = []): self
    {
        $this->expressions[] = ['sql' => "$expression as $fieldAlias", 'data' => $values];
        return $this;
    }

    /**
     * Add a count() field in the current query.
     *
     * ```php
     * $query->addCount('*');
     * $query->addCount('b.*');
     * $query->addCount('b.author', 'nb_authors');
     *
     * $total = $db->select('table','t')->addCount('*')->execute()->fetchField();
     *
     * ```

     * @param string $field The field to count. It could be '*'.
     * @param string|null $fieldAlias The alias for the expression. If null, no alias will be generated.
     */
    public function addCount(string $field, ?string $fieldAlias = null): self
    {
        $this->expressions[] = ['sql' => "count($field)" . ($fieldAlias ? " as $fieldAlias" : ''), 'data' => []];
        return $this;
    }

    /**
     *
     */
    public function leftJoin(string $tableName, string $tableAlias, string $condition): self
    {
        $tableName = $this->db->prefixTableName($tableName);
        $this->joins[] = "left join `$tableName` `$tableAlias` on $condition";
        return $this;
    }

    /**
     *
     */
    public function rightJoin(string $tableName, string $tableAlias, string $condition): self
    {
        $tableName = $this->db->prefixTableName($tableName);
        $this->joins[] = "right join `$tableName` `$tableAlias` on $condition";
        return $this;
    }

    /**
     *
     */
    public function innerJoin(string $tableName, string $tableAlias, string $condition): self
    {
        $tableName = $this->db->prefixTableName($tableName);
        $this->joins[] = "inner join `$tableName` `$tableAlias` on $condition";
        return $this;
    }

    /**
     *
     */
    public function orderBy($field, $dir): self
    {
        $this->orders[] = "$field $dir";
        return $this;
    }
    /**
     *
     */
    public function groupBy($exp): self
    {
        $this->groupByExp = $exp;
        return $this;
    }

    /**
     * @see SelectTest::testLimits()
     */
    public function limit($count): self
    {
        $this->limits = [$count];
        return $this;
    }

    /**
     *
     */
    public function limitFrom($offset, $count): self
    {
        $this->limits = [$offset, $count];
        return $this;
    }

    /**
     *
     */
    public function having(Conditions $condition): self
    {
        $this->having = $condition;
        return $this;
    }

    /**
     *
     */
    public function build(): QueryData
    {
        $args = [];
        // fields
        $what = $this->fields;
        if (!empty($this->expressions)) {
            $what = array_merge($what, array_column($this->expressions, 'sql'));
            $args = array_merge($args, ...array_column($this->expressions, 'data'));
        }
        $what = implode(',', $what);

        $from = "$this->tableName `$this->tableAlias`";
        $joins = empty($this->joins) ? '' : ' ' . implode(' ', $this->joins);

        $queryData = $this->buildConditions();
        $condSql = $queryData->getPrefixed(' where ');
        $args = array_merge($args, $queryData->parameters);

        $order = '';
        $group = '';
        $having = '';

        if (!empty($this->orders)) {
            $order .= ' order by ' . implode(', ', $this->orders);
        }
        if (!empty($this->groupByExp)) {
            $group .= ' group by ' . $this->groupByExp;
        }
        $limits = '';
        if (!empty($this->limits)) {
            $limits = ' limit ' . join(',', $this->limits);
        }
        if (!is_null($this->having)) {
            $queryData = $this->having->buildConditions();
            $having = $queryData->getPrefixed(' having ');
            $args = array_merge($args, $queryData->parameters);
        }
        $sql  = trim("select $what from $from$joins$condSql$group$order$limits$having");
        return new QueryData($sql, $args);
    }
}
