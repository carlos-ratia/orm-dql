<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Exception;

/**
 * Class FilterNull
 * @package Cratia\ORM\DQL
 */
class FilterNull implements IFilter
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
    public function getOperator(): string
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getValue()
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
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

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IFilter
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
     * @return ISql
     */
    public function toSQL(): ISql
    {
        return new Sql();
    }
}