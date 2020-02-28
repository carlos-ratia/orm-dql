<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;


/**
 * Interface IQueryDelete
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IQueryDelete
{
    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return $this
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQueryDelete;

    /**
     * @return ISql
     */
    public function toSql(): ISql;

    /**
     * @return ITable
     */
    public function getFrom(): ITable;

    /**
     * @param ITable $from
     * @return $this
     */
    public function setFrom(ITable $from): IQueryDelete;

    /**
     * @return IFilter
     */
    public function getFilter(): IFilter;

    /**
     * @param IFilter $filter
     * @return $this
     */
    public function addFilter(IFilter $filter): IQueryDelete;
}