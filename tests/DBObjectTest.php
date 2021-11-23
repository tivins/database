<?php

use Tivins\Database\{ Query, SelectQuery };
use Tivins\Database\Tests\{ TestConfig, TestBase };
use Tivins\Database\Tests\data\{ User };
use PHPUnit\Framework\TestCase;

class DBObjectTest extends TestBase
{
    public function testSave()
    {
        $sampleUserName = 'John Doe';

        $db = TestConfig::db();

        $user = new User($db);
        $user->save(['name' => $sampleUserName]);

        $user = new User($db);
        $user->load(['uid' => 1]);
        $this->assertEquals($user->getName(), $sampleUserName);

        $user = new User($db);
        $user->load(['uid' => 2]);
        $this->assertEquals($user->getName(), null);
    }
}