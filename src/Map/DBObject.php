<?php

namespace Tivins\Database\Map;

use JsonSerializable;
use ReflectionClass;
use stdClass;
use Tivins\Database\Conditions;
use Tivins\Database\Statement;

abstract class DBObject implements JsonSerializable
{
    public function __construct()
    {
    }

    abstract public function getTableName(): string;

    /**
     */
    public function loadById(int $id): static
    {
        [$pkey] = $this->getProperties();
        $this->load((new Conditions())->isEqual($pkey, $id));
        return $this;
    }

    /**
     *
     */
    public static function getInstance(int $id): static
    {
        $obj = new static();
        $obj->loadById($id);
        return $obj;
    }

    public function map(stdClass $obj): static
    {
        [$pkey, , $fields] = $this->getProperties();
        $this->{$pkey} = $obj->{$pkey};
        foreach ($fields as $k => $v) {
            $this->$k = $obj->$k;
        }
        return $this;
    }

    /**
     *
     */
    public function load(Conditions $conditions): static
    {
        $obj = DBOManager::db()->select($this->getTableName(), 'o')
            ->addFields('o')
            ->nest($conditions)
            ->execute()
            ->fetch();
        if ($obj) {
            $this->map($obj);
        }
        return $this;
    }

    /**
     * @return array
     * @todo use cache
     */
    public function getProperties(): array
    {
        $pkey    = '';
        $uniques = [];
        $fields  = [];

        $ref   = new ReflectionClass($this);
        $props = $ref->getProperties();
        foreach ($props as $prop) {
            $attrs = $prop->getAttributes(DBOAccess::class);
            if (!empty($attrs)) {
                $access = $attrs[0]->newInstance();
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

    /**
     *
     */
    public function save(): static
    {
        [$pkey, , $fields] = $this->getProperties();

        if (!$this->{$pkey}) {

            DBOManager::db()->insert($this->getTableName())
                ->fields($fields)
                ->execute();

            $this->{$pkey} = DBOManager::db()->lastId();
        }
        else {

            DBOManager::db()->update($this->getTableName())
                ->fields($fields)
                ->isEqual($pkey, $this->{$pkey})
                ->execute();
        }
        return $this;
    }

    /**
     * @return static[]
     */
    public static function loadCollection(Statement $statement): array
    {
        return $statement->fetchAllObjects(static::class);
        // return array_map(fn($obj): static => (new static())->map($obj), $data);
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

