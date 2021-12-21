<?php

namespace Tivins\Database;

use Tivins\Database\Exceptions\{ConditionException, DatabaseException};

/**
 * Base class to manage database objects. 
 */
class DBObject
{
    protected string $tableName;
    protected array $indexNames;
    protected Database $db;
    protected ?object $object;

    public function __construct(Database $db) 
    {
        $this->db = $db;
    }

    /**
     * @throws ConditionException
     * @throws DatabaseException
     */
    public function load($data): ?object
    {
        $q = $this->db->select($this->tableName, 't')
            ->addFields('t');
        $keys = $this->extractKeys($data);
        foreach ($keys as $k => $v) {
            $q->condition($k, $v);
        }
        $this->object = $q->execute()->fetch();
        return $this->object;
    }

    /**
     * @throws ConditionException
     * @throws DatabaseException
     */
    public function save($data): array
    {
        $exists = $this->load($data);
        if ($exists) { return $this->update($data); }
        return $this->insert($data);
    }

    /**
     * @throws ConditionException
     * @throws DatabaseException
     */
    private function update($data): array
    {
        $q = $this->db->update($this->tableName)
            ->fields($data);
        $keys = $this->extractKeys($data);
        foreach ($keys as $k => $v) {
            $q->condition($k, $v);
        }
        $q->execute();
        return $keys;
    }

    /**
     * @throws DatabaseException
     */
    private function insert($data): array
    {
        $this->db->insert($this->tableName)
            ->fields($data)
            ->execute();
        if (count($this->indexNames) == 1) {
            return array_combine($this->indexNames, [$this->db->lastId()]);
        }
        return $this->extractKeys($data);
    }

    private function extractKeys($data): array
    {
        return array_filter($data, fn($key) => in_array($key, $this->indexNames), ARRAY_FILTER_USE_KEY);
    }

}
