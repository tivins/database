<?php

namespace Tivins\Database\Map;

use Attribute;
use phpDocumentor\Reflection\Types\Static_;
use Tivins\Database\Database;

class DBObject implements \JsonSerializable
{

    public const TABLE = '';

    public function __construct(protected Database $db)
    {
    }

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

    public function load()
    {
        $obj = $this->db->select($this->getConst('TABLE'), 'o')
            ->addFields('o')
            ->execute()
            ->fetch();
        if ($obj) {
            [$pkey, , $fields] = $this->getProperties();
            foreach ($fields as $k => $v) {
                $this->$k = $obj->$k;
            }
        }
    }

    private function getConst($name)
    {
        return constant("static::{$name}");
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

    public function save()
    {
        [$pkey, $uniques, $fields] = $this->getProperties();

        if (!$this->{$pkey}) {

            $this->db->insert($this->getConst('TABLE'))
                ->fields($fields)
                ->execute();

            $this->{$pkey} = $this->db->lastId();
        }
        else {

            $this->db->update($this->getConst('TABLE'))
                ->fields($fields)
                ->condition($pkey, $this->{$pkey})
                ->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        [$pkey, $uniques, $fields] = $this->getProperties();

        return [$pkey => $this->{$pkey}] + $fields;
    }
}

