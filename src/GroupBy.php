<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IGroupBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\GroupByToSql;

/**
 * Class GroupBy
 * @package Cratia\ORM\DQL
 */
class GroupBy implements IGroupBy
{
    /**
     * @var IField
     */
    private $field;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSQL;

    /**
     * GroupBy constructor.
     * @param IField $field
     * @param IStrategyToSQL|null $strategyToSQL
     */
    private function __construct(IField $field, IStrategyToSQL $strategyToSQL = null)
    {
        $this->field = $field;

        if (!is_null($strategyToSQL)) {
            $this->strategyToSQL = $strategyToSQL;
        } else {
            $this->strategyToSQL = new GroupByToSql();
        }
    }

    /**
     * @inheritDoc
     */
    public function getField(): IField
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function getStrategyToSQL(): IStrategyToSQL
    {
        return $this->strategyToSQL;
    }

    /**
     * @inheritDoc
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IGroupBy
    {
        $this->strategyToSQL = $strategyToSQL;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toSQL(): ISql
    {
        return $this->getStrategyToSQL()->toSQL($this);
    }

    /**
     * @param IField $field
     * @return IGroupBy
     */
    public static function create(IField $field): IGroupBy
    {
        return new self($field);
    }

    /**
     * @return ITable[]
     */
    public function getTable()
    {
        return [$this->getField()->getTable()];
    }
}
