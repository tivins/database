<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Database;
use Tivins\Database\Exceptions\{ConnectionException, DatabaseException};

class TransactionTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testTransaction()
    {
        $db = TestConfig::db();

        /**
         * Truncate the table for the test.
         */
        $db->truncateTable('users');

        /**
         * Ensure the table is empty.
         */
        $this->assertCountUsers($db, 0);

        /**
         * Create data for the test.
         */
        $generatedName = uniqid('u', true);

        /**
         * Start the transaction.
         */
        $db->transaction();

        /**
         * Insert some data.
         */
        $db->insert('users')
            ->fields(['name' => $generatedName])
            ->execute();

        /**
         * Here, we have 1 user.
         */
        $this->assertCountUsers($db, 1);

        /**
         * The rollback() restore the table state at transaction() point.
         */
        $db->rollback();

        /**
         * So, the table should be empty.
         */
        $this->assertCountUsers($db, 0);
    }

    /**
     *
     *
     * @throws DatabaseException
     */
    private function assertCountUsers(Database $db, int $expectedCount)
    {
        $this->assertEquals($expectedCount,
            $db->select('users', 'u')
                ->addCount('*')
                ->execute()
                ->fetchField());
    }

    /**
     * @throws ConnectionException
     */
    public function testCommit()
    {
        $db = TestConfig::db();

        $db->transaction();
        $db->insert('users')
            ->fields(['name'=>'user'.time()])
            ->execute();
        $db->commit();

        $this->expectException(\PDOException::class);
        $db->rollback();
    }
}