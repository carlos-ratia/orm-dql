<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\TableToSQL;

/**
 * Class Table
 * @package Cratia\ORM\DQL
 */
class Table implements ITable
{
    /**
     * @var string
     */
    private $tableSchema;

    /**
     * @var string
     */
    private $as;

    /**
     * @var IRelation[]
     */
    private $relations;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSql;

    /**
     * Table constructor.
     * @param string $tableSchema
     * @param string|null $as
     * @param IStrategyToSQL|null $strategy
     */
    public function __construct(string $tableSchema, string $as = null, IStrategyToSQL $strategy = null)
    {
        $this->tableSchema = $tableSchema;
        if (!is_null($as)) {
            $this->as = $as;
        } else {
            $this->as = $tableSchema;
        }
        $this->relations = [];
        if (!is_null($strategy)) {
            $this->strategyToSql = $strategy;
        } else {
            $this->strategyToSql = new TableToSQL();
        }
    }

    /**
     * @inheritDoc
     */
    public function getTableSchema(): string
    {
        return $this->tableSchema;
    }

    /**
     * @inheritDoc
     */
    public function getAs(): string
    {
        return $this->as;
    }

    /**
     * @inheritDoc
     */
    public function getStrategyToSql(): IStrategyToSQL
    {
        return $this->strategyToSql;
    }

    /**
     * @inheritDoc
     */
    public function setStrategyToSql(IStrategyToSQL $strategyToSql): ITable
    {
        $this->strategyToSql = $strategyToSql;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toSQL(): ISql
    {
        return $this->getStrategyToSql()->toSQL($this);
    }

    /**
     * @inheritDoc
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @inheritDoc
     */
    public function addRelation(IRelation $relation): ITable
    {
        $this->relations[$relation->getId()] = $relation;
        return $this;
    }
}