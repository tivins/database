<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\ConditionException;

/**
 * Base class of Query.
 * Allow adding conditions on queries, like 'is null' et 'like %%', operators, ...
 */
class Conditions
{
    public const MODE_AND = 'and';
    public const MODE_OR  = 'or';

    protected static array $operators = ['<','<=','=','!=','>=','>','<=>','<>', 'between'];

    protected array $conditions = [];

    /**
     * @var Conditions[]
     */
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
     * @return string[]
     */
    public static function getOperators(): array
    {
        return self::$operators;
    }

    /**
     * @param string[] $operators
     */
    public static function setOperators(array $operators): void
    {
        self::$operators = $operators;
    }

    /**
     *
     */
    public function whereIn(string $field, array $values): self
    {
        $stmt = "$field in (" . implode(',', array_fill(0, count($values), '?')) . ")";
        $this->pushCondition($stmt, $values);
        return $this;
    }

    public function between(string $field, $low, $high): self
    {
        $this->pushCondition("$field between ? and ?", [$low, $high]);
        return $this;
    }

    /**
     * Search on the given field's value for a value matching the given value.
     *
     * @param string $value A string using `%` character(s) as wildcard (ex. `"%search%"`).
     */
    public function like(string $field, string $value): self
    {
        $this->pushCondition("$field like ?", [$value]);
        return $this;
    }

    /**
     * Search on the given field for a NULL value.
     */
    public function isNull(string $field): self
    {
        $this->pushCondition("$field is null", []);
        return $this;
    }

    /**
     * Search on the given field for a non-NULL value.
     */
    public function isNotNull(string $field): self
    {
        $this->pushCondition("$field is not null", []);
        return $this;
    }

    public function isEqual(string $field, $value): self
    {
        $this->pushCondition("$field = ?", [$value]);
        return $this;
    }

    public function isDifferent(string $field, $value): self
    {
        $this->pushCondition("$field != ?", [$value]);
        return $this;
    }

    public function nest(Conditions $field): static
    {
        $this->nestedConditions[] = $field;
        return $this;
    }

    /**
     * Add a specific condition to the current query.
     *
     * @param Conditions|string $field
     * @param null $value
     * @param string $operator
     * @return Conditions
     * @throws ConditionException
     * @see Query
     * @see https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html
     */
    public function condition(Conditions|string $field, $value = null, string $operator = '='): self
    {
        if ($field instanceof Conditions) {
            $this->nestedConditions[] = $field;
            return $this;
        }
        if ($operator == 'in') {
            if (!is_array($value)) {
                throw new ConditionException('Invalid value');
            }
            return $this->whereIn($field, $value);
        }
        if ($operator == 'like') {
            return $this->like($field, $value);
        }
        if (!in_array($operator, self::$operators)) {
            throw new ConditionException('Invalid operator');
        }
        $this->pushCondition("$field $operator ?", [$value]);
        return $this;
    }

    /**
     * Shortcut to `->condition($field,$value)` (and suppress ConditionException).
     *
     * @param string $field
     * @param mixed $value
     * @return $this
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function equalsTo(string $field, mixed $value = null): self
    {
        return $this->condition($field, $value);
    }

    /**
     * Add an expression to the current conditions.
     *
     * ```php
     * $query->conditionExpression('concat(field, ?) = another_field', $someValue);
     * ```
     *
     * @param string $expression The SQL expression.
     * @param mixed ...$args The parameters for the given expression.
     * @return self The current request.
     *
     * @see SelectTest::testConditionExpression();
     */
    public function conditionExpression(string $expression, ...$args): self
    {
        $this->pushCondition($expression, $args);
        return $this;
    }

    /**
     * Build the query string for the (nested) conditions with according parameters.
     *
     * Return_ an array with two values :
     * 1. The SQL string for the condition,
     * 2. An array containing the condition's parameters.
     *
     * This function can use a recursive call in case of nested conditions.
     * In this case, the query strings are 'glued' using their own condition mode ('and','or'),
     * and the parameters are concatenated.
     */
    public function buildConditions(): QueryData
    {
        if (empty($this->conditions) && empty($this->nestedConditions)) {
            return new QueryData();
        }

        $query = implode(' ' . $this->mode . ' ', array_column($this->conditions, 'cond'));
        $query = $this->mode == self::MODE_OR ? "($query)" : $query;
        $parameters = array_merge(...array_column($this->conditions, 'data'));

        $qData = new QueryData($query, $parameters);

        foreach ($this->nestedConditions as $k => $nestedConditions) {
            $queryData = $nestedConditions->buildConditions();
            $qData->merge($queryData, $k == 0 ? '' : " $this->mode ");
        }

        return $qData;
    }

    /**
     * Private function used by most of the conditions shortcuts.
     * Push the given condition and parameters to the current query.
     */
    private function pushCondition(string $condition, array $data): void
    {
        $this->conditions[] = [
            'cond' => $condition,
            'data' => $data,
        ];
    }
}
