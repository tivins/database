<?php

namespace Tivins\Database\Schema;

class Table
{
    private readonly string $name;
    public string $class;
    public string $comment;
    public string $pk = '';
    public string $map_id = '';
    private string $exchange = '';
    /**
     * @var Field[]
     */
    public array $fields = [];
    public array $indexes = [];

    public function __construct(string $name, string $class, string $comment = '')
    {
        $this->name    = $name;
        $this->class   = $class;
        $this->comment = $comment;
    }

    public function getPKName(): string
    {
        return $this->pk;
    }

    public function getMapId(): string
    {
        return $this->map_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function set_class(string $class): Table
    {
        $this->class = $class;
        return $this;
    }

    public function get_comment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Table
    {
        $this->comment = $comment;
        return $this;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $structName): Table
    {
        $this->exchange = $structName;
        return $this;
    }

    public function setMapId(string $map_id): Table
    {
        $this->map_id = $map_id;
        return $this;
    }

    public function linkFieldTable(string $name, Table $table): static
    {
        $this->fields[$name]->fk = [
            'table' => $table->name,
            'pk'    => $table->pk,
            'obj'   => $table,
        ];
        return $this;
    }

    public function addFields(Field ...$fields): static
    {
        foreach ($fields as $field) {
            $this->fields[$field->name] = $field;
        }
        return $this;
    }

    public function setPrimaryKey(Field $field): static
    {
        $this->pk = $field->name;
        return $this;
    }

    public function addIndex(string $name, array $array): static
    {
        $this->indexes[$name] = $array;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public static function makeLinking(string $name, string $class, Table $t1, Table $t2): static
    {
        $pk      = Field::newSerial($t1->pk . '_' . $t2->pk);
        $left    = Field::newForeign($t1->pk, $t1);
        $right   = Field::newForeign($t2->pk, $t2);
        $created = Field::newTimestamp('created', 'the creation timestamp');
        $deleted = Field::newTimestamp('deleted', 'the deletion timestamp')->setDefault(0);
        return (new static($name, $class))
            ->addFields($pk, $left, $right, $created, $deleted)
            ->setPrimaryKey($pk);
    }
}