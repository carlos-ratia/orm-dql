<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface IGroupBy
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IGroupBy
{
    /**
     * @return IField
     */
    public function getField(): IField;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return IGroupBy
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IGroupBy;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @return ITable[]
     */
    public function getTable();
}