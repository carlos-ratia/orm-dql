<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\RelationNull;
use Cratia\ORM\DQL\TableNull;
use Cratia\ORM\Strategies\SQL\MySQL\QueryRelationBag;

/**
 * Class QueryRelations
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryRelations
{
    /**
     * @var QueryRelationBag[]
     */
    private $map;

    /**
     * QueryRelations constructor.
     */
    private function __construct()
    {
        $this->map = [];
    }

    /**
     * @param IQuery $query
     * @return self
     */
    public static function create(IQuery $query)
    {
        /** @var QueryRelations $self */
        $self = new QueryRelations();

        /** @var IRelation $relation */
        foreach ($query->getRelations() as $relation) {
            if ($relation instanceof RelationNull) {
                continue;
            }
            $self->_set($relation, false);
        }
        return $self;
    }

    /**
     * @param IRelation $relation
     * @param bool $require
     * @return $this
     */
    protected function _set(IRelation $relation, bool $require): self
    {
        $this->map[$this->getKey($relation->getTable())] = new QueryRelationBag($relation, $require);
        return $this;
    }

    /**
     * @param IRelation $relation
     * @return false|QueryRelationBag
     */
    protected function _get(IRelation $relation)
    {
        return ($this->has($relation))
            ? $this->map[$this->getKey($relation->getTable())]
            : false;
    }

    /**
     * @param ITable $table
     * @return string
     */
    protected function getKey(ITable $table): string
    {
        return "{$table->getTableSchema()}::{$table->getAs()}";
    }

    /**
     * @param ITable $table
     * @return QueryRelations
     */
    public function setAsRequired(ITable $table): QueryRelations
    {
        if ($table instanceof TableNull) {
            return $this;
        }
        if (!$this->has($table)) {
            return $this;
        }
        if (($bag = $this->getByTable($table)) === false) {
            return $this;
        }
        return $this->_set($bag->getRelation(), true);
    }

    /**
     * @param IRelation $relation
     * @return bool
     */
    public function isRequired(IRelation $relation): bool
    {
        if (!$this->has($relation)) {
            return false;
        }
        return $this->_get($relation)->isRequired();
    }

    /**
     * @param ITable|IRelation $reference
     * @return bool
     */
    public function has($reference): bool
    {
        if ($reference instanceof IRelation) {
            $table = $reference->getTable();
        } elseif ($reference instanceof ITable) {
            $table = $reference;
        } else {
            return false;
        }
        if ($table instanceof TableNull) {
            return false;
        }
        return (isset($this->map[$this->getKey($table)]));
    }

    /**
     * @param ITable|IRelation $reference
     * @return QueryRelationBag|false
     */
    public function get($reference)
    {
        if (!$this->has($reference)) {
            return false;
        }

        if ($reference instanceof IRelation) {
            $table = $reference->getTable();
        } elseif ($reference instanceof IRelation) {
            $table = $reference;
        } else {
            return false;
        }

        return $this->getByTable($table);
    }

    /**
     * @param ITable $table
     * @return QueryRelationBag|false
     */
    public function getByTable(ITable $table)
    {
        if (!$this->has($table)) {
            return false;
        }
        return $this->map[$this->getKey($table)];

    }
}


