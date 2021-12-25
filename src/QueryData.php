<?php

namespace Tivins\Database;

class QueryData
{
    public function __construct(
        public string $sql = '',
        public array $parameters = [],
    )
    {
    }

    public function getPrefixed(string $string): string
    {
        if (!empty(trim($this->sql))) {
            $this->sql = $string . $this->sql;
        }
        return $this->sql;
    }

    public function merge(self $queryData, string $joinKeyword): self
    {
        $this->sql .= $queryData->getPrefixed($joinKeyword);
        $this->parameters = array_merge($this->parameters, $queryData->parameters);
        return $this;
    }
}
