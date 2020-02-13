<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface ITable
 * @package Cratia\ORM\DQL\Interfaces
 */
interface ITable
{
    /**
     * @return string
     */
    public function getTableSchema(): string;

    /**
     * @return string
     */
    public function getAs(): string;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSql(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSql
     * @return ITable
     */
    public function setStrategyToSql(IStrategyToSQL $strategyToSql): ITable;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @return IRelation[]
     */
    public function getRelations();

    /**
     * @param IRelation $relation
     * @return ITable
     */
    public function addRelation(IRelation $relation): ITable;
}