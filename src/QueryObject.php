<?php

namespace Tivins\Database;

class QueryObject
{
    /**
     * Gets the generated SQL string.
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * Gets the parameters for the generated query.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    private string $sql = '';
    private array $parameters = [];
}