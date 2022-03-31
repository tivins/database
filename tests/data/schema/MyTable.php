<?php

namespace Tivins\Database\Tests\data\schema;

use Tivins\Database\Map\DBOAccess;

class MyTable {
    #[DBOAccess(DBOAccess::UNIQ)]
    protected int $tid;
    #[DBOAccess]
    protected string $title;
    #[DBOAccess]
    protected string $description;
}