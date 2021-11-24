<?php

namespace Tivins\Database\Tests\data;

use Tivins\Database\{ DBObject };

class User extends DBObject
{
    protected string $tableName = 'users';
    protected array $indexNames = ['uid'];

    public function getName(): string {
        return $this->object->name ?? '';
    }
    public function getState(): int {
        return $this->object->state ?? 0;
    }
}