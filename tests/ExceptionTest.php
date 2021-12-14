<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};

class ExceptionTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testException()
    {
        $db = TestConfig::db();
        $this->expectException(DatabaseException::class);
        $db->select('table_not_found', 't')
                ->addFields('t')
                ->execute()
                ->fetchAll();
    }

    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testConditionException()
    {
        $db = TestConfig::db();
        $this->expectException(ConditionException::class);
        $db->select('table','t')->condition('t.field', 12, '=>');
    }
}