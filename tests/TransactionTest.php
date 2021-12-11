<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConnectionException, DatabaseException};
use Exception;
use Tivins\Database\Database;

class TransactionTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testTransaction()
    {
        $db = TestConfig::db();
        $db->truncateTable('users');
        $this->assertCountUsers($db, 0);
        $generatedName = uniqid('u', true);

        $db->transaction();
        try
        {
            $db->insert('users')
                ->fields(['name' => $generatedName])
                ->execute();

            $this->assertCountUsers($db, 1);
            throw new Exception('Interrupted');
        }
        catch (Exception $ex)
        {
            $db->rollback();
        }
        $this->assertCountUsers($db, 0);
    }

    /**
     * @throws DatabaseException
     */
    private function assertCountUsers(Database $db, int $expectedCount)
    {
        $this->assertCount($expectedCount, $db->select('users', 'u')->addFields('u')->execute()->fetchAll());
    }
}