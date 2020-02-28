<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFieldValue;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IQueryUpdate;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryUpdateToSQL;
use Exception;

/**
 * Class QueryUpdate
 * @package Cratia\ORM\DQL
 */
class QueryUpdate implements IQueryUpdate
{
    /**
     * @var ITable
     */
    private $from;

    /**
     * @var IFieldValue[]
     */
    private $fields;

    /**
     * @var FilterGroup|IFilter
     */
    private $filter;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSQL;

    /**
     * Insert constructor.
     * @param ITable|null $from
     * @param IStrategyToSQL|null $strategy
     */
    public function __construct(ITable $from = null, IStrategyToSQL $strategy = null)
    {
        $this->fields = [];
        $this->filter = FilterGroup::and();
        if (is_null($from)) {
            $this->from = new TableNull();
        } else {
            $this->from = $from;
        }
        if (is_null($strategy)) {
            $this->strategyToSQL = new QueryUpdateToSQL();
        } else {
            $this->strategyToSQL = $strategy;
        }
    }

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL
    {
        return $this->strategyToSQL;
    }

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return $this
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQueryUpdate
    {
        $this->strategyToSQL = $strategyToSQL;
        return $this;
    }

    /**
     * @return ISql
     */
    public function toSql(): ISql
    {
        return $this->getStrategyToSQL()->toSQL($this);
    }

    /**
     * @return ITable
     */
    public function getFrom(): ITable
    {
        return $this->from;
    }

    /**
     * @param ITable $from
     * @return $this
     */
    public function setFrom(ITable $from): IQueryUpdate
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return IFieldValue[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return IFilter
     */
    public function getFilter(): IFilter
    {
        return $this->filter;
    }
    
    /**
     * @param IField $field
     * @param mixed $value
     * @return $this
     * @throws Exception
     */
    public function addField(IField $field, $value): IQueryUpdate
    {
        if ($this->getFrom()->getTableSchema() !== $field->getTable()->getTableSchema()) {
            $method = __METHOD__;
            throw new Exception("Error in the {$method}(....,{$value}) -> The field must contain the same insert table, Insert table ({$this->getFrom()->getTableSchema()}) Field table ({$field->getTable()->getTableSchema()}).");
        }
        if ($field->getType() !== IField::COLUMN) {
            $method = __METHOD__;
            $type = IField::COLUMN;
            throw new Exception("Error in the {$method}(...,{$value}) -> The field must be of the type {$type}.");
        }
        $this->fields[] = new FieldValue($field, $value);
        return $this;
    }

    /**
     * @param IFilter $filter
     * @return $this
     */
    public function addFilter(IFilter $filter): IQueryUpdate
    {
        $this->getFilter()->add($filter);
        return $this;
    }
}