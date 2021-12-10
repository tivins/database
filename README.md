# A PDO Wrapper

* Requires PHP 8.0 to run

## CI & Stats

[![Build Status](https://app.travis-ci.com/tivins/database.svg?branch=main)](https://app.travis-ci.com/tivins/database)
[![Download Status](https://img.shields.io/packagist/dm/tivins/database.svg)](https://packagist.org/packages/tivins/database/stats)

## Install

```sh
composer require tivins/database
```

or

```sh
git clone git@github.com:tivins/database.git
```

## Example

```php
use Tivins\Database\Database;
use Tivins\Database\Connectors\MySQLConnector;

$db = new Database(new MySQLConnector('dbname', 'user', 'password', 'localhost'));

$users = $db->select('posts', 'p')
    ->leftJoin('users', 'u', 'p.author_id = u.id')
    ->fields('p')
    ->addField('u', 'name', 'author_name')
    ->execute()
    ->fetchAll();
```

## Summary

* [Connectors](#connectors)
* [Select query](#select-query)
* [Insert query](#insert-query)
* [Update query](#update-query)
* [Merge query](#merge-query)
* [Nested conditions](#nested-conditions)

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
$db->select('books', 'b')
    ->addFields('b')
    ->condition('b.reserved', 0)
    ->execute()
    ->fetchAll();
```

**Join** (use as well `innerJoin`, `leftJoin`).
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
$tagsQuery = App::db()->select('tags', 't')
    ->innerJoin('book_tags', 'bt', 'bt.tag_id = t.id')
    ->addFields('t')
    ->addExpression('count(bt.book_id)', 'books_count')
    ->groupBy('t.id')
    ->orderBy('t.name', 'asc');
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

## Run unit tests

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
