<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface IOrderBy
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IOrderBy
{
    /**
     * @var string
     */
    const DESC = "DESC";

    /**
     * @var string
     */
    const ASC = "ASC";

    /**
     * @return IField
     */
    public function getField(): IField;

    /**
     * @return string
     */
    public function getModeSorting(): string;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return IOrderBy
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IOrderBy;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @return ITable[]
     */
    public function getTable();
}