<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IOrderBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Exception;

/**
 * Class OrderByNull
 * @package Cratia\ORM\DQL
 */
class OrderByNull implements IOrderBy
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getField(): IField
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getModeSorting(): string
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getStrategyToSQL(): IStrategyToSQL
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IOrderBy
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
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
    public function getTable()
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }
}