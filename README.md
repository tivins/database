# A PDO Wrapper

A fluent, lightweight, and efficient PDO wrapper.

## CI & Stats

[![Build Status](https://app.travis-ci.com/tivins/database.svg?branch=main)](https://app.travis-ci.com/tivins/database)
[![Download Status](https://img.shields.io/packagist/dm/tivins/database.svg)](https://packagist.org/packages/tivins/database/stats)

## Install

### Requirements

* PHP >= 8.0
* PDO extension
* (dev-only) PHPUnit

### Download

```sh
# composer
composer require tivins/database
# github
git clone git@github.com:tivins/database.git
```

## Example

```php

use Tivins\Database\{Database, Connectors\MySQLConnector};

$db = new Database(new MySQLConnector('dbname', 'user', 'password', 'localhost'));

$users = $db->select('posts', 'p')
    ->leftJoin('users', 'u', 'p.author_id = u.id')
    ->addFields('p')
    ->addField('u', 'name', 'author_name')
    ->execute()
    ->fetchAll();
```

## Summary

* [Connectors](#connectors)
* Queries :
  * [Select query](#select-query)
  * [Insert query](#insert-query)
  * [Update query](#update-query)
  * [Merge query](#merge-query)
  * [Nested conditions](#nested-conditions)
  * [Expressions](#expressions)
  * [Having](#having)
  * [Transactions](#transactions)
* [Error handling](#error-handling)
* [Unit tests](#unit-tests)

## Usage

### Connectors

Create a `Database` instance require a valid `Connector`.

```php
# MySQL
$connector = new MySQLConnector('dbname', 'user', 'password');
# SQLite
$connector = new SQLiteConnector('path/to/file');
```

### Create queries

Both usages below are valid:

```php
// from database object
$query = $db->select('users', 'u');
// from new object
$query = new SelectQuery($db, 'users', 'u');
```

### Select query

**Basic**
```php
$data = $db->select('books', 'b')
    ->addFields('b')
    ->condition('b.reserved', 0)
    ->execute()
    ->fetchAll();
```

**Join** 

use as well `innerJoin`, `leftJoin`.

```php
$db->select('books', 'b')
    ->addFields('b', ['id', 'title'])
    ->leftJoin('users', 'u', 'u.id = b.owner')
    ->addField('u', 'name', 'owner_name')
    ->condition('b.reserved', 1)
    ->execute()
    ->fetchAll();
```

**Expression**
```php
$db->select('books', 'b')
    ->addField('b', 'title')
    ->addExpression('concat(title, ?)', 'some_field', time())
    ->condition('b.reserved', 0)
    ->execute()
    ->fetchAll();
```

**Group by**
```php
$tagsQuery = $db->select('tags', 't')
    ->innerJoin('book_tags', 'bt', 'bt.tag_id = t.id')
    ->addFields('t')
    ->addExpression('count(bt.book_id)', 'books_count')
    ->groupBy('t.id')
    ->orderBy('t.name', 'asc');
```

**Range/Limit**
```php
$query->limit(10);          # implicit start from 0.
$query->limitFrom(0, 10);   # explicit start from 0.
$query->limitFrom(100, 50); # will fetch 50 rows from 100th row.
```

### Insert query
```php
$db->insert('book')
    ->fields([
        'title' => 'Book title',
        'author' => 'John Doe',
    ])
    ->execute();
```

#### Insert expressions

Expressions can be used inside the array given to `fields()` function.


```php
$db->insert('geom')
    ->fields([
        'name'     => $name,
        'position' => new InsertExpression('POINT(?,?)', $x, $y)
    ])
    ->execute();
```

Will execute a following query/arguments
```sql
insert into `geom` (`name`, `position`) values (?, POINT(?,?))
```
```php
[$name, $x, $y]
```

InsertExpression are also allowed with a MergeQuery.

### Update query

```php
$db->update('book')
    ->fields(['reserved' => 1])
    ->condition('id', 123)
    ->execute();
```

### Merge query

```php
$db->merge('book')
    ->keys(['ean' => '123456'])
    ->fields(['title' => 'Book title', 'author' => 'John Doe'])
    ->execute();
```

### Delete query

Perform a `delete` query on the given table.
All methods of [`Conditions`][4] can be used on a [`DeleteQuery`][3] object.

```php
$db->delete('book')
    ->whereIn('id', [3, 4, 5])
    ->execute();
```

## Expressions

You can use `SelectQuery::addExpression()` to add an expression to the selected fields.

Signature : `->addExpression(string $expression, string $alias, array $args)`.

```php
$query = $db->select('books', 'b')
    ->addExpression('concat(title, ?)', 'some_field', time())
    ->execute();
```

## Conditions

Some examples:

```php
->condition('field', 2);      // eg: where field = 2
->condition('field', 2, '>'); // eg: where field > 2
->condition('field', 2, '<'); // eg: where field < 2
->whereIn('field', [2,6,8]);  // eg: where field int (2,6,8)
->like('field', '%search%');  // eg: where field like '%search%'
->isNull('field');            // eg: where field is null
->isNotNull('field');         // eg: where field is not null
```

### Nested conditions

Conditions are available for [`SelectQuery`][1], [`UpdateQuery`][2] and [`DeleteQuery`][3].

```php
$db->select('book', 'b')
    ->fields('b', ['id', 'title', 'author'])
    ->condition(
        $db->or()
        ->condition('id', 3, '>')
        ->like('title', '%php%')
    )
    ->execute();
```
And below is equivalent:

```php
$db->select('book', 'b')
    ->fields('b', ['id', 'title', 'author'])
    ->condition(
        (new Conditions(Conditions::MODE_OR))
        ->condition('id', 3, '>')
        ->like('title', '%php%')
    )
    ->execute();
```

## Having

```php
$db->select('maps_polygons', 'p')
    // ->...
    ->having($db->and()->isNotNull('geom'))
    ->execute()
    //...
    ;
```

## Transactions

```php
use Tivins\Database{ Database, DatabaseException, MySQLConnector };
function makeSomething(Database $db)
{
    $db->transaction()
    try {
        // do some stuff
    }
    catch (DatabaseException $exception) {
        $db->rollback();
        // log exception...
    }
}
```

## Error handling

There are three main exception thrown by Database.

* [ConnectionException][5], raised by the Database constructor, if a connection cannot be established.
* [DatabaseException][7], thrown when a PDO exception is raised from the query execution.
* [ConditionException][6], raised when a given operator is not allowed.

All of these exceptions has an explicit message (from PDO, essentially).

Usage short example:

```php
try {
    $this->db = new Database($connector);
}
catch (ConnectionException $exception) {
    $this->logErrorInternally($exception->getMessage());
    $this->displayError("Cannot join the database.");
}
```
```php
try {
    $this->db->insert('users')
        ->fields([
            'name' => 'DuplicateName',
        ])
        ->execute();  
}
catch (DatabaseException $exception) {
    $this->logErrorInternally($exception->getMessage());
    $this->displayError("Cannot create the user.");
}
```

## Unit tests

Create a test database, and a grant to a user on it.
Add a `phpunit.xml` at the root of the repository.

```mysql
/* NB: This is a quick-start example. */
create database test_db;
create user test_user@localhost identified by 'test_passwd';
grant all on test_db.* to test_user@localhost;
flush privileges;
```

```xml
<phpunit>
    <php>
        <env name="DB_NAME" value="test_db"/>
        <env name="DB_USER" value="test_user"/>
        <env name="DB_PASS" value="test_password"/>
        <env name="DB_HOST" value="localhost"/>
    </php>
</phpunit>
```

Then, run unit tests

```bash
vendor/bin/phpunit tests/
```

[1]: /src/SelectQuery.php
[2]: /src/UpdateQuery.php
[3]: /src/DeleteQuery.php
[4]: /src/Conditions.php
[5]: /src/Exceptions/ConnectionException.php
[6]: /src/Exceptions/ConditionException.php
[7]: /src/Exceptions/DatabaseException.php