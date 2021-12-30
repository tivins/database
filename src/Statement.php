<?php

namespace Tivins\Database;

use PDO;
use PDOStatement;

class Statement
{
    public function __construct(
        private PDOStatement $statement
    ) {
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function fetchAll(): array
    {
        return $this->statement->fetchAll();
    }

    /**
     * @param string $key
     * @param string|null $value The name of the column for the value of returned object.
     *      If value is null, the value of returned data will the full-row object.
     * @return array
     */
    public function fetchAssocKey(string $key, string|null $value = null): array
    {
        $data = $this->statement->fetchAll();
        return array_combine(array_column($data, $key), $value ? array_column($data, $value) : $data);
    }

    public function fetch(): ?object
    {
        return ($res = $this->statement->fetch()) ? $res : null;
    }

    public function fetchField(): string
    {
        return $this->statement->fetchColumn();
    }

    public function fetchCol(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_COLUMN);
    }
}
