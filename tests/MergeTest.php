<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};

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
        $this->assertEquals(0, $this->getNumUsers());


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

        $this->assertEquals($username, $user->name);
        $this->assertEquals(1, $user->state);
        $this->assertEquals(1, $merge->getObject()?->uid);

        $merge = $db->merge('users')
            ->keys(['name' => $username])
            ->fields(['name' => $username, 'state' => 0]);
        $merge->execute();

        $user = $db->select('users', 'u')
            ->condition('name', $username)
            ->addFields('u')
            ->execute()
            ->fetch();

        $this->assertEquals($username, $user->name);
        $this->assertEquals(0, $user->state);


        $this->assertEquals(1, $this->getNumUsers());
    }
}