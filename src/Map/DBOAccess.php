<?php

namespace Tivins\Database\Map;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DBOAccess
{
    const DEFAULT = 0;
    const PKEY    = 1;
    const UNIQ    = 2;

    public function __construct(public int $mode = self::DEFAULT)
    {
    }

    public function isUnique(): bool
    {
        return $this->mode == self::UNIQ;
    }
}