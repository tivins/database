<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Database;
use Tivins\Database\Exceptions;

class FailureCallbackTest extends TestBase
{
    /**
     * @throws Exceptions\ConnectionException
     * @throws Exceptions\DatabaseException
     */
    function testCallback()
    {
        $db = TestConfig::db();

        $passed = '';
        $failure = function(Database $db, string $message) use(&$passed) {
            $passed = $message;
            return true;
        };
        $db->setFailureCallback($failure);
        $db->select('unknown_table', 't')->execute();
        $this->assertNotEmpty($passed);
        $db->setFailureCallback(null); // restore default value
    }
}