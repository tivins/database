<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\ConditionException;
use Tivins\Database\Exceptions\ConnectionException;
use Tivins\Database\Exceptions\DatabaseException;
use function PHPUnit\Framework\assertEquals;

class LogCallbackTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     * @throws ConditionException
     */
    public function test()
    {
        $db = TestConfig::db();

        $db->setLogCallback(function (string $sql, array $parameter) {
            $this->assertEquals('select u.* from t_users `u` where u.uid = ?', $sql);
            $this->assertEquals([2], $parameter);
        });

        $db->select('users', 'u')
            ->condition('u.uid', 2)
            ->addFields('u')
            ->execute();

        $db->setLogCallback(null);

        // Run another query to ensure that the callback will not be triggered anymore.
        $db->query('show tables', []);
    }
}