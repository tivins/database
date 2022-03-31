<?php

namespace Tivins\Database\Map;

use ReflectionException;
use Tivins\Database\Database;

class Builder
{
    private string $tableName = '';
    /**
     * @throws ReflectionException
     */
    public function __construct(private Database $database, string $className)
    {
        $class = new \ReflectionClass($className);
        $this->tableName = $class->getShortName();


        $fields = $class->getProperties();
        foreach ($fields as $field) {
            var_dump($field->getName());
            $attrs = $field->getAttributes();
            if (!empty($attrs) && $attrs[0]->getName() == DBOAccess::class) {
                $attrInstance = $attrs[0]->newInstance();
                var_dump($attrInstance->isUnique());
            }
        }


        $tableName = [];


    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }



}