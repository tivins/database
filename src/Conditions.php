<?php

namespace Tivins\Database;
use Tivins\Database\Exceptions\ConditionException;

/**
 *
 */
class Conditions
{
    public const MODE_AND = 'and';
    public const MODE_OR  = 'or';

    protected array $conditions = [];
    protected array $nestedConditions = [];
    protected string $mode = self::MODE_AND;

    /**
     *
     */
    public function __construct(string $mode = self::MODE_AND)
    {
        $this->mode = $mode;
    }

    /**
     *
     */
    public function whereIn(string $field, array $values): self
    {
        $stmt = "$field in (".implode(',',array_fill(0, count($values), '?')).")";
        $this->pushCondition($stmt, $values);
        return $this;
    }

    /**
     *
     */
    public function like(string $field, string $value): self
    {
        $this->pushCondition("$field like ?", [$value]);
        return $this;
    }

    /**
     *
     */
    public function isNull(string $field): self
    {
        $this->pushCondition("$field is null", []);
        return $this;
    }

    /**
     *
     */
    public function isNotNull(string $field): self
    {
        $this->pushCondition("$field is not null", []);
        return $this;
    }

    /**
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html
     * @throws ConditionException
     */
    public function condition($field, $value = null, $operator = '='): self
    {
        if ($field instanceof Conditions)
        {
            $this->nestedConditions[] = $field;
            return $this;
        }

        if ($operator == 'in') return $this->whereIn($field, $value);
        if ($operator == 'like') return $this->like($field, $value);
        if (!in_array($operator, ['<','<=','=','!=','>=','>','<=>','<>'])) throw new ConditionException('Invalid operator');
        $this->pushCondition("$field $operator ?", [$value]);
        return $this;
    }

    /**
     *
     */
    public function buildConditions(): array
    {
        if (empty($this->conditions) && empty($this->nestedConds)) {
            return ['', []];
        }

        $query = implode(' ' . $this->mode . ' ', array_column($this->conditions, 'cond'));
        if ($this->mode == self::MODE_OR) $query = "($query)";
        $parameters = array_flatten(array_column($this->conditions, 'data'));

        foreach ($this->nestedConditions as $nestedConditions) {
            [$subQuery, $subParameters] = $nestedConditions->buildConditions();
            if (!empty($subQuery)) {
                $query .= (empty($query) ? '' : ' ' . $this->mode . ' ') . $subQuery;
                $parameters = array_merge($parameters, $subParameters);
            }
        }

        return [$query, $parameters];
    }

    /**
     *
     */
    private function pushCondition(string $condition, array $data): void
    {
        $this->conditions[] = [
            'cond' => $condition,
            'data' => $data,
        ];
    }
}
