<?php

namespace Tivins\Database\Schema;


abstract class Schema
{
    /** @var Enum[] Enumerations */
    private array $enums = [];
    /** @var Table[] */
    private array $tables = [];
    private string $comment = '';
    private string $namespace;

    abstract public function build(): static;

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return Table[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    public function getTable(string $name): Table|null
    {
        return $this->tables[$name] ?? null;
    }

    /**
     * @return Enum[]
     */
    public function getEnums(): array
    {
        return $this->enums;
    }

    public function getEnum(string $name): Enum|null
    {
        return $this->enums[$name] ?? null;
    }

    public function setMembers(Table|Enum ...$members): static
    {
        foreach ($members as $member) {
            if ($member instanceof Table) {
                $this->tables[$member->getName()] = $member;
            } else {
                $this->enums[$member->name] = $member;
            }
        }
        return $this;
    }

    public function setEnums(Enum ...$enums): static
    {
        foreach ($enums as $enum) {
            $this->enums[$enum->name] = $enum;
        }
        return $this;
    }

    public function setTables(Table ...$tables): static
    {
        foreach ($tables as $table) {
            $this->tables[$table->getName()] = $table;
        }
        return $this;
    }

    protected function setNamespace(string $name, string $comment): static
    {
        $this->namespace = $name;
        $this->comment   = $comment;
        return $this;
    }
}