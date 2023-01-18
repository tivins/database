<?php

namespace Tivins\Database\Enums;

enum MergeOperation
{
    case NONE;
    case SELECT;
    case INSERT;
}
