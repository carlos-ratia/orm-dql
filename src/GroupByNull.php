<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IGroupBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Exception;

/**
 * Class GroupByNull
 * @package Cratia\ORM\DQL
 */
class GroupByNull implements IGroupBy
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
    public function getStrategyToSQL(): IStrategyToSQL
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IGroupBy
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @return ISql
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