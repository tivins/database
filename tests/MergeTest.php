<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Enums\MergeOperation;
use Tivins\Database\Exceptions\ConditionException;
use Tivins\Database\Exceptions\ConnectionException;
use Tivins\Database\Exceptions\DatabaseException;

class MergeTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    private function getNumUsers(): int
    {
        return TestConfig::db()->select('users', 'u')
        ->addCount('*')
        ->execute()
        ->fetchField();
    }
    /**
     * @throws ConnectionException | DatabaseException | ConditionException
     */
    public function testMerge()
    {
        $db = TestConfig::db();
        $username = 'user_' . time();

        $db->truncateTable('users');
        self::assertEquals(0, $this->getNumUsers());


        $merge = $db->merge('users')
            ->keys(['name' => $username])
            ->fields(['name' => $username, 'state' => 1]);

        $merge->build();
        $this->assertNull($merge->getObject());

        $merge->execute();

        $user = $db->select('users', 'u')
            ->condition('name', $username)
            ->addFields('u')
            ->execute()
            ->fetch();

        self::assertEquals($username, $user->name);
        self::assertEquals(1, $user->state);
        self::assertEquals(1, $merge->getObject()?->uid);

        $merge = $db->merge('users')
            ->keys(['name' => $username])
            ->fields(['name' => $username, 'state' => 0]);
        $merge->execute();

        $user = $db->select('users', 'u')
            ->condition('name', $username)
            ->addFields('u')
            ->execute()
            ->fetch();

        self::assertEquals($username, $user->name);
        self::assertEquals(0, $user->state);


        self::assertEquals(1, $this->getNumUsers());
    }

    /**
     * @throws ConnectionException
     */
    public function testExisting()
    {
        $db = TestConfig::db();

        // Cleanup table for tests
        $db->truncateTable('users');
        self::assertEquals(0, $this->getNumUsers());

        // Insert a user
        $db->insert('users')->fields(['name' => 'test1', 'state' => 1])->execute();
        self::assertEquals(1, $this->getNumUsers());

        // Test select
        $qry = $db->selectInsert('users')->matching(['name' => 'test1', 'state' => 1]);
        $obj = $qry->fetch();
        self::assertEquals(MergeOperation::SELECT, $qry->getProcessedOperation());
        self::assertEquals(1, $obj->uid);

        // Test insert
        $qry = $db->selectInsert('users')->matching(['name' => 'test2', 'state' => 1]);
        $obj = $qry->fetch();
        self::assertEquals(MergeOperation::INSERT, $qry->getProcessedOperation());
        self::assertEquals(2, $obj->uid);

        // Test insert + fields
        $qry = $db->selectInsert('users')
            ->matching(['name' => 'test3'])
            ->fields(['name' => 'test3', 'state' => 3]);
        $obj = $qry->fetch();
        self::assertEquals(MergeOperation::INSERT, $qry->getProcessedOperation());
        self::assertEquals(3, $obj->uid);

        // Test select + values of previous fields
        $qry = $db->selectInsert('users')->matching(['name' => 'test3']);
        $obj = $qry->fetch();
        self::assertEquals(MergeOperation::SELECT, $qry->getProcessedOperation());
        self::assertEquals(3, $obj->state);
    }
}