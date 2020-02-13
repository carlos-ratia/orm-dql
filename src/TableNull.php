<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Exception;

/**
 * Class TableNull
 * @package Cratia\ORM\DQL
 */
class TableNull implements ITable
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAs(): string
    {
        throw new Exception(
            "Error in the TableNull::getAs() -> you cannot access a as in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getTableSchema(): string
    {
        throw new Exception(
            "Error in the TableNull::getTableSchema() -> you cannot access a schema in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getStrategyToSql(): IStrategyToSQL
    {
        throw new Exception(
            "Error in the TableNull::getStrategyToSql() -> you cannot access a strategy in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setStrategyToSql(IStrategyToSQL $strategyToSql): ITable
    {
        throw new Exception(
            "Error in the TableNull::setStrategyToSql() -> you cannot access a strategy in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toSQL(): ISql
    {
        return new Sql();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRelations()
    {
        throw new Exception(
            "Error in the TableNull::getRelations() -> you cannot access a relations in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function addRelation(IRelation $relation): ITable
    {
        throw new Exception(
            "Error in the TableNull::addRelation() -> you cannot access a relation in a null object"
        );
    }
}