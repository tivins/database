<?php

namespace Tivins\Database;

class DBObject
{
    protected string $tableName;
    protected array $indexNames;
    protected Database $db;
    protected ?object $object;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    private function extractKeys($data)
    {
        return array_filter($data, fn($key) => in_array($key, $this->indexNames), ARRAY_FILTER_USE_KEY);
    }

    public function load($data)
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

    public function save($data)
    {
        $exists = $this->load($data);
        if ($exists) { return $this->update($data); }
        return $this->insert($data);
    }

    private function update($data)
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

    private function insert($data)
    {
        $this->db->insert($this->tableName)
            ->fields($data)
            ->execute();
        if (count($this->indexNames) == 1) {
            return array_combine($this->indexNames, [$this->db->lastId()]);
        }
        return $this->extractKeys($data);
    }
}
