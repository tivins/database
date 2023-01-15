<?php

namespace Tivins\Database\Schema;

class Enum
{
    public function __construct(
        public readonly string $name = '',
        public readonly string $type = 'int',
        public readonly string $comment = '',
        public readonly array $cases = [],
        //public APIAccess|null $access = null,
    ) {
    }
}
