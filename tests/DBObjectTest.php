<?php

use Tivins\Database\{ Query, SelectQuery };
use Tivins\Database\Tests\{ TestConfig, TestBase };
use Tivins\Database\Tests\data\{ User };
use PHPUnit\Framework\TestCase;

class DBObjectTest extends TestBase
{
    public function testSave()
    {
        $sampleUserName1 = 'John Doe1';
        $sampleUserName2 = 'John Doe2';

        $db = TestConfig::db();

        $user = new User($db);
        $user->save(['uid' => 0,'name' => $sampleUserName1]);

        $user = new User($db);
        $user->save(['uid' => 0,'name' => $sampleUserName2]);

        $user = new User($db);
        $this->assertEquals($user->load(['uid' => 1]), (object)['uid'=>1,'name'=>$sampleUserName1,'state'=>0]);
        $user->save(['uid' => 1, 'state' => 1]);

        $user = new User($db);
        $this->assertEquals($user->load(['uid' => 1]), (object)['uid'=>1,'name'=>$sampleUserName1,'state'=>1]);

        $user = new User($db);
        $this->assertEquals($user->load(['uid' => 2]), (object)['uid'=>2,'name'=>$sampleUserName2,'state'=>0]);
    }
}