<?php

namespace Tivins\Database;

use PDO;
use PDOStatement;

class Statement
{
    public function __construct(
        private PDOStatement $statement
    )
    {}

    public function fetchAll(): array {
        return $this->statement->fetchAll();
    }
    public function fetch(): ?object {
        return ($res = $this->statement->fetch()) ? $res : null;
    }
    public function fetchField(): string {
        return $this->statement->fetchColumn();
    }
    public function fetchCol(): array {
        return $this->statement->fetchAll(PDO::FETCH_COLUMN);
    }
}
