<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};

class DeleteTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException | ConditionException
     */
    public function testDelete()
    {
        $db    = TestConfig::db();
        $query = $db->delete('users')
            ->condition('uid', 2);
        $this->checkQuery($query, 'delete from `t_users` where uid = ?', [2]);
    }
}