<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\{ConditionException, DatabaseException};

/**
 *
 */
class MergeQuery extends UpdateQuery
{
    private array $keys = [];

    /**
     * @param array<string, mixed> $data
     */
    public function keys(array $data): self
    {
        $this->keys = $data;
        return $this;
    }

    /**
     * @throws ConditionException | DatabaseException
     */
    public function build(): array
    {
        $select = $this->db->select($this->tableName, 't');
        foreach ($this->keys as $key => $value) {
            $select->addField('t', $key);
            $select->condition('t.' . $key, $value);
        }
        $data = $select->execute()->fetch();
        if (! $data) {
            foreach ($this->keys as $key => $value) {
                unset($this->fields[$key]);
            }
            $query = $this->db->insert($this->tableName)->fields($this->fields);
        }
        else {
            $query = $this->db->update($this->tableName)->fields($this->fields);
            foreach ($this->keys as $key => $value) {
                $query->condition($key, $value);
            }
        }
        return $query->build();
    }
}