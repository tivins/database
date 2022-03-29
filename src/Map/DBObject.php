<?php

namespace Tivins\Database\Map;

use Tivins\Database\Database;

abstract class DBObject implements \JsonSerializable
{
    public function __construct(protected Database $db)
    {
    }

    abstract public function getTableName(): string;

    public function loadById(int $id): static
    {
        [$pkey, $uniques, $fields] = $this->getProperties();
        $this->{$pkey} = $id;
        $this->load();
        return $this;
    }

    public static function getInstance(Database $db, int $id): static
    {
        $obj = new static($db);
        $obj->loadById($id);
        return $obj;
    }

    public function load(): static
    {
        $obj = $this->db->select($this->getTableName(), 'o')
            ->addFields('o')
            ->execute()
            ->fetch();
        if ($obj) {
            [$pkey, , $fields] = $this->getProperties();
            foreach ($fields as $k => $v) {
                $this->$k = $obj->$k;
            }
        }
        return $this;
    }


    public function getProperties(): array
    {
        $pkey    = '';
        $uniques = [];
        $fields  = [];

        $ref   = new \ReflectionClass($this);
        $props = $ref->getProperties();
        foreach ($props as $prop) {
            $attrs = $prop->getAttributes(DBOAccess::class);
            if (!empty($attrs)) {
                $access = $attrs[0]->newInstance();// var_dump($attrs[0]->getName());
                if ($access->mode & DBOAccess::PKEY) {
                    $pkey = $prop->getName();
                }
                else {
                    if ($access->mode & DBOAccess::UNIQ) {
                        $uniques[] = $prop->getName();
                    }
                    $fields[$prop->getName()] = $prop->getValue($this);
                }
            }
        }
        return [$pkey, $uniques, $fields];
    }

    public function save(): static
    {
        [$pkey, $uniques, $fields] = $this->getProperties();

        if (!$this->{$pkey}) {

            $this->db->insert($this->getTableName())
                ->fields($fields)
                ->execute();

            $this->{$pkey} = $this->db->lastId();
        }
        else {

            $this->db->update($this->getTableName())
                ->fields($fields)
                ->condition($pkey, $this->{$pkey})
                ->execute();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        [$pkey, , $fields] = $this->getProperties();

        return [$pkey => $this->{$pkey}] + $fields;
    }
}

