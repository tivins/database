<?php
namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\ConnectionException;
use Tivins\Database\Map\DBOAccess;
use Tivins\Database\Map\DBObject;
use Tivins\Database\Tests\TestConfig;

class World extends DBObject
{
    public const TABLE = 'world';

    #[DBOAccess(DBOAccess::PKEY)]
    protected int $wid = 0;
    #[DBOAccess(DBOAccess::UNIQ)]
    protected string $name = '';
    #[DBOAccess()]
    protected string $info = '';

    /**
     * @return int
     */
    public function getWid(): int
    {
        return $this->wid;
    }

    /**
     * @param int $wid
     * @return World
     */
    public function setWid(int $wid): World
    {
        $this->wid = $wid;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return World
     */
    public function setName(string $name): World
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo(): string
    {
        return $this->info;
    }

    /**
     * @param string $info
     * @return World
     */
    public function setInfo(string $info): World
    {
        $this->info = $info;
        return $this;
    }

}

class DBOTest extends TestBase
{
    /**
     * @throws ConnectionException
     */
    public function testCreate()
    {
        $db    = TestConfig::db();

        $db->dropTable('world')
            ->create('world')
            ->addAutoIncrement('wid')
            ->addString('name')
            ->addString('info')
            ->addUniqueKey(['name'])
            ->execute();

        $world = new World($db);
        $world->setName("Test");
        $world->save();

        $worlds = $db->select('world', 'w')->addFields('w')->execute()->fetchAll();
        self::assertEquals('[{"wid":1,"name":"Test","info":""}]', json_encode($worlds));

        $world->setName('Test-changed');
        $world->setInfo("Hello world");
        $world->save();

        $worlds = $db->select('world', 'w')->addFields('w')->execute()->fetchAll();
        self::assertEquals('[{"wid":1,"name":"Test-changed","info":"Hello world"}]', json_encode($worlds));
        
        $world = World::getInstance($db, 1);
        self::assertEquals('{"wid":1,"name":"Test-changed","info":"Hello world"}', json_encode($world));

    }
}