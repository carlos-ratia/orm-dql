<?php


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IHaving;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterGroupToWhereConditionSQL;

/**
 * Class Having
 * @package Cratia\ORM\DQL
 */
class Having extends FilterGroup implements IHaving
{
    /**
     * FilterGroup constructor.
     * @param IFilter[] $filters
     * @param bool $mode
     * @param IStrategyToSQL $strategyToSql
     */
    protected function __construct(array $filters, bool $mode, IStrategyToSQL $strategyToSql)
    {
        parent::__construct($filters, $mode, $strategyToSql);
    }

    /**
     * @param IStrategyToSQL|null $strategy
     * @return IHaving
     */
    public static function create(IStrategyToSQL $strategy = null): IHaving
    {
        if (is_null($strategy)) {
            $strategy = new FilterGroupToWhereConditionSQL();
        }
        return new Having([], IFilter::MODE_INCLUSIVE, $strategy);
    }

    /**
     * @param IFilter $filter
     * @return IHaving
     */
    public function addCondition(IFilter $filter): IHaving
    {
        parent::add($filter);
        return $this;
    }
}