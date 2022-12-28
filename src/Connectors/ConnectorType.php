<?php

namespace Tivins\Database\Connectors;

enum ConnectorType
{
    case NONE;
    case MYSQL;
    case SQLITE;
}
