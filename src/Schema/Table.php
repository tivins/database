<?php

namespace Tivins\Database\Schema;

class Table
{
    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var Field[]
     */
    private array $fields = [];

    /**
     * @var array
     */
    private array $indexes = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }


    // --- generated getters/setters ---


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Table
     */
    public function setName(string $name): Table
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     * @return Table
     */
    public function setFields(array $fields): Table
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param array $indexes
     * @return Table
     */
    public function setIndexes(array $indexes): Table
    {
        $this->indexes = $indexes;
        return $this;
    }
}