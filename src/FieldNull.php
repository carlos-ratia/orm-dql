<?php


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Exception;

/**
 * Class FieldNull
 * @package Cratia\ORM\DQL
 */
class FieldNull implements IField
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getType(): string
    {
        throw new Exception(
            "Error in the FieldNull::getType() -> you cannot access a type in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getTable(): ITable
    {
        throw new Exception(
            "Error in the FieldNull::getTable() -> you cannot access a table in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getColumn(): string
    {
        throw new Exception(
            "Error in the FieldNull::getColumn() -> you cannot access a column in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAs(): string
    {
        throw new Exception(
            "Error in the FieldNull::getAs() -> you cannot access a as in a null object"
        );
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCallback(): callable
    {
        throw new Exception(
            "Error in the FieldNull::getCallback() -> you cannot access a callback in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IField
    {
        throw new Exception(
            "Error in the FieldNull::setStrategyToSQL() -> you cannot access a strategy in a null object"
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getStrategyToSQL(): IStrategyToSQL
    {
        throw new Exception(
            "Error in the FieldNull::getStrategyToSQL() -> you cannot access a strategy in a null object"
        );
    }

    /**
     * @inheritDoc
     */
    public function toSQL(): ISql
    {
        return new Sql();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function isCallback(): bool
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }
}