<?php

namespace Tivins\Database\Schema;



class Field
{
    public array $fk = [];
    public bool $selectable = false;
    public bool $unique = false;
    public bool $not_empty = false;
    public bool $not_null = false;
    public bool $valid_email = false;
    public int $length = 0;
    public mixed $default;
    public string|bool $exchange = false;
    public Enum|null $enum = null;
    public string $translation_map = '';
    public bool $index = false;

    public function __construct(
        public readonly string $name = '',
        public FieldType $type = FieldType::STRING,
        public string $comment = '',
    ) {
    }

    public function getEnum(): Enum|null
    {
        return $this->enum;
    }

    public function setEnum(Enum $enum): static
    {
        $this->enum = $enum;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @todo As of PHP 8.2, change "bool" to "true"
     */
    public function setExchange(bool|string $exchange): static
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function getExchange(): string|bool
    {
        return $this->exchange;
    }

    public function hasDefault(): bool
    {
        return isset($this->default);
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function setDefault(mixed $default): static
    {
        $this->default = $default;
        return $this;
    }

    public function isValidEmail(): bool
    {
        return $this->valid_email;
    }

    /**
     * Ensure that data contains a valid email (not null, not empty).
     */
    public function setValidEmail(): static
    {
        $this->valid_email = true;
        $this->not_empty   = true;
        $this->not_null    = true;
        return $this;
    }

    public function isNotEmpty(): bool
    {
        return $this->not_empty;
    }

    /**
     * Implique not_null.
     * @see isNotNull()
     */
    public function setNotEmpty(): static
    {
        $this->not_empty = true;
        $this->not_null  = true;
        return $this;
    }

    public function isNotNull(): bool
    {
        return $this->not_null;
    }

    public function setNotNull(): static
    {
        $this->not_null = true;
        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function setUnique(): static
    {
        $this->unique = true;
        return $this;
    }

    /**
     * @return array{table:string,pk:string}
     */
    public function get_fk(): array
    {
        return $this->fk;
    }

    public function setForeignPrimaryKey(Table $table): static
    {
        $this->fk = ['table' => $table, 'pk' => $table->pk];
        return $this;
    }

    public function isSelectable(): bool
    {
        return $this->selectable;
    }

    /**
     * Allow to fetch data from database using this field.
     */
    public function setSelectable(): static
    {
        $this->selectable = true;
        return $this;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function setType(FieldType $type): Field
    {
        $this->type = $type;
        return $this;
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

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): Field
    {
        $this->length = $length;
        return $this;
    }

    public function getTranslationMap(): string
    {
        return $this->translation_map;
    }

    public function setTranslationMap(string $translation_map): static
    {
        $this->translation_map = $translation_map;
        return $this;
    }

    public function isIndex(): bool
    {
        return $this->index;
    }

    public function setIndex(): Field
    {
        $this->index = true;
        return $this;
    }

    public static function newSerial(string $name, string $comment = ''): static
    {
        return new static($name, FieldType::SERIAL, $comment);
    }

    public static function newTimestamp(string $name, string $comment = ''): static
    {
        return new static($name, FieldType::TIMESTAMP, $comment);
    }

    public static function newForeign(string $name, Table $foreignClass, string $comment = ''): static
    {
        return (new static($name, FieldType::UINT, $comment))
            ->setDefault(0)
            ->setForeignPrimaryKey($foreignClass);
    }

    public static function newEnum(string $name, Enum $enum, string $comment = ''): static
    {
        return (new static($name, FieldType::ENUM, $comment))
            ->setEnum($enum);
    }

    public static function newString(string $name, int $length, string $comment = ''): static
    {
        return (new static($name, FieldType::STRING, $comment))
            ->setLength($length);
    }

    public static function newText(string $name, string $comment = ''): static
    {
        return (new static($name, FieldType::TEXT, $comment));
    }

    public static function newUint(string $name, int $default = 0, string $comment = ''): static
    {
        return (new static($name, FieldType::UINT, $comment))
            ->setDefault($default);
    }

    public static function newInt(string $name, int $default = 0, string $comment = ''): static
    {
        return (new static($name, FieldType::INT, $comment))
            ->setDefault($default);
    }

    public static function newFloat(string $name, float $default = 0.0, string $comment = ''): static
    {
        return (new static($name, FieldType::FLOAT, $comment))
            ->setDefault($default);
    }
}