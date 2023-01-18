<?php

namespace Tivins\Database\Tests;

class UserClass {
    public int $uid;
    public string $name;
    public bool $state;

    public function getName(): string {
        return "**{$this->name}**";
    }
}

class FetchTest extends \PHPUnit\Framework\TestCase
{
    public function testFetchObject()
    {
        $db = TestConfig::db();
        TestConfig::initializeTables();

        $db->truncateTable('users');
        $db->insert('users')->fields(['name' => 'test1'])->execute();
        $db->insert('users')->fields(['name' => 'test2', 'state' => 2])->execute();

        $results = $db->select('users', 'u')
            ->addFields('u')
            ->execute()
            ->fetchAllObjects(UserClass::class);

        self::assertEquals(UserClass::class, get_class($results[0]));
        self::assertEquals('**test1**', $results[0]->getName());
        /*create('users')
            ->addAutoIncrement('uid', true)
            ->addString('name', nullable: false)
            ->addBool('state', 0)
            ->addUniqueKey(['name'])
            ->execute();
        */
    }
}