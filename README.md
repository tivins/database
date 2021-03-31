# A PDO Wrapper

* Requires PHP 8.0 to run

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

### Nested conditions

Conditions are available for `SelectQuery`, `UpdateQuery` and `DeleteQuery`.

```php
$db->select('book', 'b')
    ->fields('b', ['id', 'title', 'author'])
    ->condition(
        $db->or()
        ->condition('id', 3, '>')
        ->condition('title', '%php%', 'like')
    )
    ->execute();
```
