<?php


namespace Cratia\ORM\DQL\Interfaces;


use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterGroupToWhereConditionSQL;

interface IHaving extends IFilter
{
    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @param IFilter $filter
     * @return IHaving
     */
    public function addCondition(IFilter $filter): IHaving;
}