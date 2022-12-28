<?php

namespace Tivins\Database\Map;

use ReflectionClass;
use ReflectionException;
use Tivins\Database\CreateQuery;
use Tivins\Database\Database;

/**
 * Builds a create query from a DBObject.
 */
class Builder
{
    private string $tableName;
    private array $fields;

    /**
     *
     * @param Database $database
     * @param string $className The DBObject class
     * @throws ReflectionException
     */
    public function __construct(string $className)
    {
        $class = new ReflectionClass($className);
        $this->tableName = $class->getShortName();


        $fields = $class->getProperties();
        foreach ($fields as $field) {
            // var_dump($field->getName());
            $attrs = $field->getAttributes();
            if (!empty($attrs) && $attrs[0]->getName() == DBOAccess::class) {
                $attrInstance = $attrs[0]->newInstance();
                // var_dump($attrInstance->isUnique());
                $this->fields[$field->getName()] = [
                    'attrs' => $attrInstance,
                    'type' => $field->getType()->getName()
                ];
            }
        }
        var_dump($this->fields);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getCreateQuery(Database $database): CreateQuery
    {
        $cQuery = $database->create($this->tableName);
        foreach ($this->fields as $name => $dbAccess)
        {

        }
        return $cQuery;
    }
}