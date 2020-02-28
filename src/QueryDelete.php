<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IQueryDelete;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryDeleteToSQL;

/**
 * Class QueryDelete
 * @package Cratia\ORM\DQL
 */
class QueryDelete implements IQueryDelete
{
    /**
     * @var ITable
     */
    private $from;

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
        $this->filter = FilterGroup::and();
        if (is_null($from)) {
            $this->from = new TableNull();
        } else {
            $this->from = $from;
        }
        if (is_null($strategy)) {
            $this->strategyToSQL = new QueryDeleteToSQL();
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
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQueryDelete
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
    public function setFrom(ITable $from): IQueryDelete
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return IFilter
     */
    public function getFilter(): IFilter
    {
        return $this->filter;
    }

    /**
     * @param IFilter $filter
     * @return $this
     */
    public function addFilter(IFilter $filter): IQueryDelete
    {
        $this->getFilter()->add($filter);
        return $this;
    }
}