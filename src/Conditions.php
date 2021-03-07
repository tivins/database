<?php

namespace Tivins\Database;

/**
 *
 */
class Conditions
{
    protected array $conditions;

    /**
     *
     */
    public function whereIn(string $field, array $values)
    {
        $stmt = "$field in (".implode(',',array_fill(0, count($values), '?')).")";
        $this->pushCondition($stmt, $values);
        return $this;
    }

    /**
     *
     */
    public function like(string $field, string $value)
    {
        $this->pushCondition("$field like ?", [$value]);
        return $this;
    }

    /**
     *
     */
    public function condition($field, $value, $operator = '=')
    {
        if ($operator == 'in') return $this->whereIn($field, $value);
        if ($operator == 'like') return $this->like($field, $value);
        if (!in_array($operator, ['<','<=','=','!=','>=','>'])) throw new Exception('Invalid operator');
        $this->pushCondition("$field $operator ?", [$value]);
        return $this;
    }

    /**
     *
     */
    public function buildConditions()
    {
        if (empty($this->conditions)) return['', []];

        $query = 'where ' . implode(' and ', array_column($this->conditions, 'cond'));
        $parameters = array_flatten(array_column($this->conditions, 'data'));
        return [$query, $parameters];
    }

    /**
     *
     */
    private function pushCondition(string $condition, array $data)
    {
        $this->conditions[] = [
            'cond' => $condition,
            'data' => $data,
        ];
    }
}
