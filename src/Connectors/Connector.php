<?php

namespace Tivins\Database\Connectors;

use PDO;

interface Connector
{
    public function connect(): PDO;
}
