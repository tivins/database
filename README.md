# A PDO Wrapper

## Example

```php
use Tivins\Database\Database;
use Tivins\Database\Connectors\MySQLConnector;

$connector = new MySQLConnector('dbname', 'user', 'password');
$db = new Database($connector);

$users = $db->select('posts', 'p')
    ->leftJoin('users', 'u', 'p.author_id = u.id')
    ->fields('p')
    ->addField('u', 'name', 'author_name')
    ->execute()
    ->fetchAll();

```

## Usage

### Connectors

MySQL
```php
$connector = new MySQLConnector('dbname', 'user', 'password');
$db = new Database($connector);
```

SQLite
```php
$connector = new SQLiteConnector('path/to/file');
$db = new Database($connector);
```

### Create queries

Both usages below are valid:

```php
// from database object
$query = $db->select('users', 'u'); // recommanded
// from new object
$query = new SelectQuery($db, 'users', 'u');
```

### Select query

**Basic**
```php
$db->select('books', 'b')
    ->addField('b', 'title')
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

