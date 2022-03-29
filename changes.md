# Changes

### 2022-03-29

* `DBObject` is now an abstract class.
* `DBObject::TABLE` constant does no longer exist and should be now provided by a method:
   ```php
  public function getTableName(): string;
  ```
