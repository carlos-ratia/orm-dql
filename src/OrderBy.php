<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IOrderBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\OrderByToSQL;

/**
 * Class OrderBy
 * @package Cratia\ORM\DQL
 */
class OrderBy implements IOrderBy
{
    /**
     * @var IField
     */
    private $field;

    /**
     * @var string
     */
    private $modeSorting;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSQL;

    /**
     * OrderBy constructor.
     * @param IField $field
     * @param string $modeSorting
     * @param IStrategyToSQL|null $strategyToSQL
     */
    public function __construct(IField $field, string $modeSorting, IStrategyToSQL $strategyToSQL = null)
    {
        $this->field = $field;
        $this->modeSorting = $modeSorting;

        if (!is_null($strategyToSQL)) {
            $this->strategyToSQL = $strategyToSQL;
        } else {
            $this->strategyToSQL = new OrderByToSQL();
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
     * @return string
     */
    public function getModeSorting(): string
    {
        return $this->modeSorting;
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
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IOrderBy
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
     * @return IOrderBy
     */
    public static function asc(IField $field): IOrderBy
    {
        return new self($field, IOrderBy::ASC);
    }

    /**
     * @param IField $field
     * @return IOrderBy
     */
    public static function decs(IField $field): IOrderBy
    {
        return new self($field, IOrderBy::DESC);
    }

    /**
     * @return ITable[]
     */
    public function getTable()
    {
        return [$this->getField()->getTable()];
    }
}