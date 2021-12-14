<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};

class ExceptionTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testException()
    {
        $db = TestConfig::db();
        $this->expectException(DatabaseException::class);
        $db->select('table_not_found', 't')->addFields('t')->execute();
    }

    /**
     * @throws ConnectionException | ConditionException | DatabaseException
     */
    public function testConditionException()
    {
        $db = TestConfig::db();
        $this->expectException(ConditionException::class);
        $db->and()->condition('t.field', 12, '=>');
    }
}