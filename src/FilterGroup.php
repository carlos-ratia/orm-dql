<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterGroupToWhereConditionSQL;
use Exception;

/**
 * Class FilterGroup
 * @package Cratia\ORM\DQL
 */
class FilterGroup implements IFilter
{
    /**
     * @var IFilter[]
     */
    private $filters;

    /**
     * @var bool
     */
    private $mode;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSql;

    /**
     * FilterGroup constructor.
     * @param IFilter[] $filters
     * @param bool $mode
     * @param IStrategyToSQL $strategyToSql
     */
    protected function __construct($filters, bool $mode, IStrategyToSQL $strategyToSql)
    {
        $this->filters = $filters;
        $this->mode = $mode;
        $this->strategyToSql = $strategyToSql;
    }

    /**
     * @param IStrategyToSQL|null $strategy
     * @return FilterGroup
     */
    public static function and(IStrategyToSQL $strategy = null): FilterGroup
    {
        if (is_null($strategy)) {
            $strategy = new FilterGroupToWhereConditionSQL();
        }
        $self = new FilterGroup([], IFilter::MODE_INCLUSIVE, $strategy);
        return $self;
    }

    /**
     * @param IStrategyToSQL|null $strategy
     * @return FilterGroup
     */
    public static function or(IStrategyToSQL $strategy = null): FilterGroup
    {
        if (is_null($strategy)) {
            $strategy = new FilterGroupToWhereConditionSQL();
        }
        $self = new FilterGroup([], IFilter::MODE_EXCLUSIVE, $strategy);
        return $self;
    }

    /**
     * @param IFilter $filter
     * @return $this
     */
    public function add(IFilter $filter): self
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return IFilter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return bool
     */
    public function getMode(): bool
    {
        return $this->mode;
    }

    /**
     * @inheritDoc
     */
    public function getStrategyToSql(): IStrategyToSQL
    {
        return $this->strategyToSql;
    }

    /**
     * @inheritDoc
     */
    public function setStrategyToSql(IStrategyToSQL $strategyToSql): IFilter
    {
        $this->strategyToSql = $strategyToSql;
        return $this;
    }

    public function toSQL(): ISql
    {
        return $this->getStrategyToSql()->toSQL($this);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getField(): IField
    {
        throw new Exception(
            "Error in the FilterGroup::getField() -> you cannot access a field in a filter group"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getOperator(): string
    {
        throw new Exception(
            "Error in the FilterGroup::getOperator() -> you cannot access a field in a filter group"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getValue()
    {
        throw new Exception(
            "Error in the FilterGroup::getValue() -> you cannot access a field in a filter group"
        );
    }

    /**
     * @inheritDoc
     */
    public function getTable()
    {
        /** @var ITable[] $tables */
        $tables = [];
        /** @var IFilter $filter */
        foreach ($this->getFilters() as $filter) {
            $tables = array_merge($tables, $filter->getTable());
        }
        return $tables;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->filters) === 0;
    }
}