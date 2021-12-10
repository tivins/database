<?php

namespace Tivins\Database;

class CreateQuery extends Query
{
    private array $fields = [];

    private array $indexes = [];

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
     * Ex: `$query->addString('field', nullable: false, length: 64)`
     *
     * @param string $name The column name.
     * @param int $length The length of the string.
     * @param bool $nullable Is it nullable or not ?
     * @return $this
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

    public function setUnique(array $columns): self
    {
        $this->indexes[] = ['type' => 'unique', 'columns' => $columns];
        return $this;
    }

    public function build(): array
    {
        $statements = [];
        foreach ($this->fields as $field) {
            $statements[] = "`$field[name]` $field[type] $field[attr]";
        }
        foreach ($this->indexes as $index) {
            $statements[] = $index['type'] . ' (' . implode(',', $index['columns']) . ')';
        }

        $statements = implode(', ', $statements);
        $sql = "create table if not exists `$this->tableName` ($statements)";
        return [$sql, []];
    }
}