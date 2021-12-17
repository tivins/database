<?php

namespace Tivins\Database;

use BackedEnum;
use UnitEnum;

/**
 * Most of the parameters of this object are intended to be used with named-parameters.
 */
class CreateQuery extends Query
{
    private array $fields = [];

    private array $indexes = [];

    private string $engine = 'InnoDB';

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function setEngine(string $engine): CreateQuery
    {
        $this->engine = $engine;
        return $this;
    }

    public function addAutoIncrement(string $name, bool $unsigned = true): self
    {
        $this->fields[] = [
            'type' => 'int',
            'attr' => ($unsigned ? 'unsigned' : '') . ' auto_increment',
            'name' => $name
        ];
        $this->indexes[] = ['type' => 'primary key', 'columns' => [$name]];
        return $this;
    }

    /**
     * This function is indented to be called using named parameters.
     *
     * Example:
     * ```php
     * $query->addString('field', nullable: false, length: 64)
     * ```
     *
     * @param string $name The column name.
     * @param int $length The length of the string.
     * @param bool $nullable Is it nullable or not ?
     * @return $this The current object.
     */
    public function addString(string $name, int $length = 255, bool $nullable = true): self
    {
        $this->fields[] = [
            'type' => 'varchar(' . $length . ')',
            'attr' => ($nullable ? '' : ' not null'),
            'name' => $name,
        ];
        return $this;
    }

    public function addBool(string $name, int $default): self
    {
        $this->fields[] = [
            'type' => 'tinyint(1)',
            'attr' => 'not null default ' . $default,
            'name' => $name,
        ];
        return $this;
    }

    public function addText(string $name, bool $nullable = true): self
    {
        $this->fields[] = [
            'type' => 'text',
            'attr' => ($nullable ? '' : ' not null'),
            'name' => $name,
        ];
        return $this;
    }

    /**
     * This function is indented to be called using named parameters.
     *
     * Examples:
     * ```php
     * $query->addInteger('field', -1);
     * $query->addInteger('field', 0, nullable: false);
     * $query->addInteger('field', null, nullable: true, unsigned: true);
     * ```
     *
     * @param string $name The column name.
     * @param int|null $default The default value. If `null` is given, the $nullable will automatically set to `true`.
     * @param bool $unsigned Does the integer unsigned or not ?
     * @param bool $nullable Can column contain null value or not ?
     * @return $this The current object.
     */
    public function addInteger(string $name, ?int $default, bool $unsigned = false, bool $nullable = false): self
    {
        $this->fields[] = [
            'type' => 'int' . ($unsigned ? ' unsigned' : ''),
            'attr' => trim(($nullable || is_null($default) ? '' : 'not null')
                    . ' default ' . (is_null($default) ? 'null' : $default)),
            'name' => $name,
        ];
        return $this;
    }

    public function addGeometry(string $name): self
    {
        $this->fields[] = ['type' => 'geometry', 'attr' => '', 'name' => $name];
        return $this;
    }

    /**
     * Add SQL enum from PHP enum.
     *
     *     Enum Fruits { case Apple; case Banana; }
     *     $query->addEnum('fruits', Fruits::cases());
     *
     * @param string $name
     * @param UnitEnum[] $cases
     *      Use the `value` property of the BackedEnum if available, or `name` property of the Enum otherwise.
     *
     * @param UnitEnum|null $default
     * @return $this
     */
    public function addEnum(string $name, array $cases, ?UnitEnum $default = null): self
    {
        $values = array_map(fn(UnitEnum $enum) => '"' . ($enum->value ?? $enum->name) . '"', $cases);
        $this->fields[] = ['type' => 'enum(' . implode(',', $values) . ')', 'attr' => '', 'name' => $name];
        return $this;
    }

    public function addJSON(string $name, ?string $default = null, bool $nullable = true): self
    {
        $this->fields[] = [
            'type' => 'json',
            'attr' => trim(($nullable || is_null($default) ? '' : 'not null')
                . ' default ' . (is_null($default) ? 'null' : $default)),
            'name' => $name];
        return $this;
    }

    public function addUniqueKey(array $columns): self
    {
        $this->indexes[] = ['type' => 'unique', 'columns' => $columns];
        return $this;
    }

    public function addIndex(array $columns): self
    {
        $this->indexes[] = ['type' => 'index', 'columns' => $columns];
        return $this;
    }

    public function build(): array
    {
        $statements = [];
        foreach ($this->fields as $field) {
            $statements[] = trim("`$field[name]` $field[type] $field[attr]");
        }
        foreach ($this->indexes as $index) {
            $statements[] = $index['type'] . ' (' . implode(',', $index['columns']) . ')';
        }
        $statements = implode(', ', $statements);
        $sql = "create table if not exists `$this->tableName` ($statements) engine=$this->engine";
        return [$sql, []];
    }

}