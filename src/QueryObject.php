<?php

namespace Tivins\Database;

class QueryObject
{
    public function __construct(
        public string $sql = '',
        public array $parameters = [],
    )
    {
    }
}
