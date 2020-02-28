<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;


use Exception;

/**
 * Interface IQueryUpdate
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IQueryUpdate
{
    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return $this
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQueryUpdate;

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
    public function setFrom(ITable $from): IQueryUpdate;

    /**
     * @return IFieldValue[]
     */
    public function getFields(): array;

    /**
     * @return IFilter
     */
    public function getFilter(): IFilter;

    /**
     * @param IField $field
     * @param mixed $value
     * @return $this
     * @throws Exception
     */
    public function addField(IField $field, $value): IQueryUpdate;

    /**
     * @param IFilter $filter
     * @return $this
     */
    public function addFilter(IFilter $filter): IQueryUpdate;
}