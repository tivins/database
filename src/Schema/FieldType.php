<?php

namespace Tivins\Database\Schema;

enum FieldType
{
    case SERIAL;
    case INT;
    case UINT;
    case BYTE;
    case BOOL;
    case STRING;
    case TEXT;
    case FLOAT;
    case DOUBLE;
    case TIMESTAMP;
    case ENUM;
}