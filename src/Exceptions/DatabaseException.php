<?php

namespace Tivins\Database\Exceptions;

use Exception;
use PDOException;

class DatabaseException extends Exception
{
    public function __construct(PDOException $exception)
    {
        parent::__construct($exception->getMessage(), (int) $exception->getCode(), $exception->getPrevious());
    }
}