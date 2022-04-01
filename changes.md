# Changes

### 2022-04-01

* Add `Conditions::isEqual()` and `Conditions::isDifferent()` shortcuts.
* Add [`DBObject::map()`](src/Map/DBObject.php).
* Add `Conditions::nest()`.

### 2022-03-29

* `DBObject` is now an abstract class.
* `DBObject::TABLE` constant does no longer exist and should be now provided by a method:
   ```php
  public function getTableName(): string;
  ```
