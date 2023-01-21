<?php

namespace Tivins\Database\Tests;

use Tivins\Database\Exceptions\{ConditionException, ConnectionException, DatabaseException};

class SelectTest extends TestBase
{
    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testSelect()
    {
        $db    = TestConfig::db();
        $query = $db->select('test', 't')
            ->addFields('t');
        $this->checkQuery($query,
            'select `t`.* from t_test `t`',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testSelectFieldWithoutAlias()
    {
        $db    = TestConfig::db();
        $query = $db->select('test', 't')
            ->addField('t', 'id');
        $this->checkQuery($query,
            'select `t`.`id` from t_test `t`',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testSelectFieldAlias()
    {
        $db    = TestConfig::db();
        $query = $db->select('test', 't')
            ->addField('t', 'id', 't_id');
        $this->checkQuery($query,
            'select `t`.`id` as `t_id` from t_test `t`',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testSelectJoin()
    {
        $db = TestConfig::db();

        $query = $db->select('test', 't')
            ->addField('t', 'id', 't_id')
            ->leftJoin('other', 'o', 'o.oid = t.id');
        $this->checkQuery($query,
            'select `t`.`id` as `t_id` from t_test `t` left join `t_other` `o` on o.oid = t.id',
            []
        );

        $query = $db->select('test', 't')
            ->addField('t', 'id', 't_id')
            ->innerJoin('other', 'o', 'o.oid = t.id');
        $this->checkQuery($query,
            'select `t`.`id` as `t_id` from t_test `t` inner join `t_other` `o` on o.oid = t.id',
            []
        );


        $query = $db->select('test', 't')
            ->addField('t', 'id', 't_id')
            ->rightJoin('other', 'o', 'o.oid = t.id');
        $this->checkQuery($query,
            'select `t`.`id` as `t_id` from t_test `t` right join `t_other` `o` on o.oid = t.id',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testOrderBy()
    {
        $db = TestConfig::db();

        $query = $db->select('test', 't')
            ->addFields('t')
            ->groupBy('t.value');
        $this->checkQuery($query,
            'select `t`.* from t_test `t` group by t.value',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testNull()
    {
        $db    = TestConfig::db();
        $query = $db
            ->select('test', 't')
            ->addFields('t')
            ->isNull('t.field')
            ->isNotNull('t.another_field');
        $this->checkQuery($query,
            'select `t`.* from t_test `t` where t.field is null and t.another_field is not null',
            []
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testGroupBy()
    {
        $db = TestConfig::db();

        $query = $db->select('geometries', 'g')
            ->addExpression('ST_Simplify(g.geom, 1024)', 'geom')
            ->having($db->and()->isNotNull('geom'));

        $this->checkQuery($query,
            'select ST_Simplify(g.geom, 1024) as `geom` from t_geometries `g` having geom is not null',
            []
        );

    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testLimits()
    {
        $db = TestConfig::db();

        $db->truncateTable('users');
        foreach (range(0, 19) as $index) {
            $db->insert('users')
                ->fields(['name' => 'user' . $index])
                ->execute();
        }

        $num = $db->select('users', 'u')
            ->addCount('*', 'count')
            ->execute()
            ->fetchField();
        $this->assertEquals(20, $num);

        $query = $db->select('users', 'u')
            ->addFields('u')
            ->limit(5)
            ->orderBy('u.uid', 'asc');
        $this->checkQuery($query, 'select `u`.* from t_users `u` order by u.uid asc limit 5', []);
        $results = $query->execute()->fetchAll();
        $this->assertCount(5, $results);
        $this->assertEquals('user0', reset($results)->name);


        $query     = $db->select('users', 'u')
            ->addFields('u')
            ->limitFrom(5, 5)
            ->orderBy('u.uid', 'asc');
        $statement = $query->execute();
        $results   = $statement->fetchAll();
        $this->assertCount(5, $results);
        $this->assertEquals(5, $statement->rowCount());
        $this->assertEquals('user5', reset($results)->name);
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testConditionExpression()
    {
        $db = TestConfig::db();

        $db->truncateTable('users');
        foreach (range(0, 19) as $index) {
            $db->insert('users')
                ->fields(['name' => 'user' . $index])
                ->execute();
        }

        $query = $db->select('users', 'u')
            ->conditionExpression('concat("user",?) = name', 12)
            ->addFields('u');

        $this->checkQuery($query, 'select `u`.* from t_users `u` where concat("user",?) = name', [12]);

        $query->execute()->fetchAll();
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testConditionBetween()
    {
        $db    = TestConfig::db();
        $query = $db->select('table', 't')
            ->addFields('t')
            ->between('x', 2, 6);
        $this->checkQuery($query,
            'select `t`.* from t_table `t` where x between ? and ?',
            [2, 6]
        );

        $query = $db->select('table', 't')
            ->addFields('t')
            ->between('x', 2, 6)
            ->between('y', -2, 2);
        $this->checkQuery($query,
            'select `t`.* from t_table `t` where x between ? and ? and y between ? and ?',
            [2, 6, -2, 2]
        );
    }

    /**
     * @throws ConnectionException | DatabaseException
     */
    public function testConditionExpression2()
    {
        $db = TestConfig::db();

        $query = $db->select('table', 't')
            ->nest(
                $db->or()
                    ->isEqual('a', 2)
                    ->isEqual('b', 6)
            )
            ->nest(
                $db->or()
                    ->isEqual('p', 7)
                    ->isEqual('j', 8)
            )
            ->addFields('t');
        $this->checkQuery($query,
            'select `t`.* from t_table `t` where (a = ? or b = ?) and (p = ? or j = ?)',
            [2, 6, 7, 8]
        );
    }

    public function testNested()
    {
        $db = TestConfig::db();

        $conditions = $db->and();

        $conditions->nest(
            $db->or()
                ->isEqual('a', 2)
                ->isEqual('b', 3)
        );
        $conditions->nest(
            $db->and()
                ->isDifferent('c', 4)
        );


        $query = $db->select('table', 't')
            ->addFields('t')
            ->nest($conditions);
        $this->checkQuery($query,
            'select `t`.* from t_table `t` where (a = ? or b = ?) and c != ?',
            [2, 3, 4]
        );
    }

    /**
     * @throws ConnectionException
     * @throws DatabaseException
     */
    public function testFetchCol()
    {
        $db = TestConfig::db();
        $db->truncateTable('users');
        foreach (range(0, 19) as $index) {
            $db->insert('users')
                ->fields(['name' => 'user' . $index])
                ->execute();
        }
        $out = $db->select('users', 'u')
            ->addField('u', 'name')
            ->orderBy('u.uid', 'asc')
            ->limit(3)
            ->execute()
            ->fetchCol();
        $this->assertEquals(['user0', 'user1', 'user2'], $out);
    }

    /**
     * @throws ConnectionException|ConditionException
     */
    public function testNestedConditions()
    {
        $db = TestConfig::db();

        $query = $db->select('users', 'u')
            ->condition(
                $db->or()
                    // like
                    ->like('u.name', 'user%')
                    ->condition('u.name', '%user', 'like')
                    // in
                    ->whereIn('u.uid', [2, 4, 6])
                    ->condition('u.state', [0, 1], 'in')
            )
            ->addFields('u', ['uid']);

        $this->checkQuery(
            $query,
            'select `u`.`uid` from t_users `u` where (u.name like ? or u.name like ? or u.uid in (?,?,?) or u.state in (?,?))',
            ['user%', '%user', 2, 4, 6, 0, 1]
        );
        $query->execute();
    }

    /**
     * @throws ConnectionException
     * @throws ConditionException
     */
    public function testFetchRowFailure()
    {
        $db = TestConfig::db();
        $this->expectException(DatabaseException::class);
        $result = $db->fetchRow('unknown', 'field', 123);
        $this->assertNull($result);
    }
}
