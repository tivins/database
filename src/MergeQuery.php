<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\{ConditionException, DatabaseException};

/**
 *
 */
class MergeQuery extends UpdateQuery
{
    private array $keys = [];
    private ?object $object = null;

    /**
     * @param array<string, mixed> $data
     */
    public function keys(array $data): self
    {
        $this->keys = $data;
        return $this;
    }

    /**
     * @throws ConditionException
     * @throws DatabaseException
     */
    private function findObject(): ?object
    {
        $select = $this->db->select($this->tableName, 't');
        $primaries = $this->db->getPrimary($this->tableName);
        $select->addFields('t', $primaries);
        foreach ($this->keys as $key => $value) {
            $select->addField('t', $key);
            $select->condition('t.' . $key, $value);
        }
        return $select->execute()->fetch();
    }

    /**
     * @throws ConditionException | DatabaseException
     */
    public function build(): QueryData
    {
        $this->object = $this->findObject();
        if (! $this->object) {
            $query = $this->db->insert($this->tableName)->fields($this->fields);
        }
        else {
            foreach ($this->keys as $key => $value) {
                unset($this->fields[$key]);
            }
            $query = $this->db->update($this->tableName)->fields($this->fields);
            foreach ($this->keys as $key => $value) {
                $query->condition($key, $value);
            }
        }
        return $query->build();
    }

    /**
     * @throws ConditionException
     * @throws DatabaseException
     */
    public function execute(): Statement
    {
        $statement = $this->executeQueryData($this->build());
        if (! $this->object) {
            $this->object = $this->findObject();
        }
        return $statement;
    }

    /**
     * Get the object fetched during build().
     */
    public function getObject(): object|null
    {
        return $this->object;
    }
}
