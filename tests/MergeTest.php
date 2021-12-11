<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConnectionException, DatabaseException};
use Exception;

class MergeTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws DatabaseException
     * @throws Exception
     */
    public function testMerge()
    {
        $db = TestConfig::db();

        $username = 'user_' . time();

        $db->merge('users')
            ->keys(['uid' => 0])
            ->fields(['name' => $username])
            ->execute();

        $user = $db->select('users', 'u')
            ->condition('name', $username)
            ->addFields('u')
            ->execute()
            ->fetch();

        $this->assertEquals($username, $user->name);
    }
}