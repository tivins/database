<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException};
use Tivins\Database\InsertExpression;

class UpdateTest extends TestBase
{
    /**
     * @throws ConnectionException
     * @throws ConditionException
     */
    public function testUpdateExpression()
    {
        $db = TestConfig::db();

        $db->truncateTable('users')
            ->insert('users')
            ->fields(['name' => 'TestUpdate0'])
            ->execute();

        $query = $db->update('users')
            ->condition('uid', 1)
            ->fields([
                'name' => new InsertExpression('concat(name,?,uid)', 'test'),
            ]);
        $query->execute();

        $object = $db->select('users', 'u')->addFields('u')->execute()->fetch();
        $this->assertEquals('TestUpdate0test1', $object->name);
    }
}