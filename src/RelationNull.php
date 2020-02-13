<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Exception;

/**
 * Class RelationNull
 * @package Cratia\ORM\DQL
 */
class RelationNull implements IRelation
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getId(): string
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getLeft(): IField
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRight(): IField
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getType(): string
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getFilters(): FilterGroup
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
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IRelation
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
    public function getTable(): ITable
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function addFilter(IFilter $filter): IRelation
    {
        $msj = __METHOD__;
        throw new Exception("Error in the {$msj}() -> not implemented.");
    }
}