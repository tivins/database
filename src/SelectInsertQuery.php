<?php

namespace Tivins\Database;

use Tivins\Database\Enums\MergeOperation;

/**
 * Create a Select/Insert Query
 *
 * **basic usage**
 *
 *      $qry = $db->selectInsert('users')->matching(['name' => 'test', 'state' => 1]);
 *      $qry->fetch()->id; // 1
 *      $qry->getProcessedOperation(); // MergeOperation::INSERT
 *
 *      $qry = $db->selectInsert('users')->matching(['name' => 'test', 'state' => 1]);
 *      $qry->fetch()->id; // 1
 *      $qry->getProcessedOperation(); // MergeOperation::SELECT
 *
 * **Insert data**
 *
 *      $matches = ['email' => 'user@example.com'];
 *      $obj = $db->selectInsert('users')
 *          ->matching($matches)
 *          ->fields($matches + ['name' =>  'user', 'created' => time()])
 *          ->fetch();
 *
 */
class SelectInsertQuery
{
    private array          $matches   = [];
    private ?array         $fields    = null;
    private MergeOperation $operation = MergeOperation::NONE;

    public function __construct(
        private readonly Database $database,
        private readonly string   $table,
    )
    {
    }

    /**
     * Defines all exact matches for where statement in the select query.
     * ex: `['name' => 'test', 'state' => 2]`.
     * @param array $matches
     * @return $this
     */
    public function matching(array $matches): static
    {
        $this->matches = $matches;
        return $this;
    }

    /**
     * Defines all fields used in the insert query.
     * If NULL, the array of `matching()` will be used.
     * @param array|null $fields
     * @return $this
     */
    public function fields(?array $fields = null): static
    {
        $this->fields = $fields;
        return $this;
    }

    public function getProcessedOperation(): MergeOperation
    {
        return $this->operation;
    }

    public function fetch(): object|null
    {
        $conditions = $this->database->and();
        foreach ($this->matches as $k => $v) {
            $conditions->condition($k, $v);
        }
        $id = $this->database->select($this->table, 'n')
            ->addFields('n')
            ->condition($conditions)
            ->execute()
            ->fetch();
        if ($id) {
            $this->operation = MergeOperation::SELECT;
            return $id;
        }
        $this->database->insert($this->table)
            ->fields($this->fields ?? $this->matches)
            ->execute();

        $this->operation = MergeOperation::INSERT;

        return $this->database->select($this->table, 'n')
            ->addFields('n')
            ->condition($conditions)
            ->execute()
            ->fetch();
    }
}