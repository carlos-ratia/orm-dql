<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;


use Exception;

/**
 * Interface IQueryInsert
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IQueryInsert
{
    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return $this
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQueryInsert;

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
    public function setFrom(ITable $from): IQueryInsert;

    /**
     * @return IFieldValue[]
     */
    public function getFields(): array;

    /**
     * @param IField $field
     * @param mixed $value
     * @return $this
     * @throws Exception
     */
    public function addField(IField $field, $value): IQueryInsert;
}