<?php

namespace Tivins\Database\Schema;

class Field
{
    private string $name          = '';
    private string $type          = 'string';
    private int    $length        = 0;
    private bool   $autoIncrement = false;
    private bool   $unsigned      = false;
    private bool   $allowNull     = true;
    private mixed  $default       = null;

    // -----------------------------------
    // ↓    Generated getters/setters    ↓
    // -----------------------------------

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Field
     */
    public function setName(string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @param bool $autoIncrement
     * @return Field
     */
    public function setAutoIncrement(bool $autoIncrement): Field
    {
        $this->autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    /**
     * @param bool $allowNull
     * @return Field
     */
    public function setAllowNull(bool $allowNull): Field
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @param mixed|null $default
     * @return Field
     */
    public function setDefault(mixed $default): Field
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type): Field
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return Field
     */
    public function setLength(int $length): Field
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @param bool $unsigned
     * @return Field
     */
    public function setUnsigned(bool $unsigned): Field
    {
        $this->unsigned = $unsigned;
        return $this;
    }


}