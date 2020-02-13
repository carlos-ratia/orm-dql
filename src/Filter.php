<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterToWhereConditionSQL;
use Exception;

/**
 * Class Filter
 * @package Cratia\ORM\DQL
 */
class Filter implements IFilter
{
    /**
     * @var IField
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var IField|ValueNull|number|boolean|string|string[]|number[]|mixed[]
     */
    private $value;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSql;

    /**
     * Filter constructor.
     * @param IField $field
     * @param string $operator
     * @param mixed|null $value
     * @param IStrategyToSQL|null $strategyToSql
     */
    private function __construct(
        IField $field,
        string $operator,
        $value = null,
        IStrategyToSQL $strategyToSql = null
    )
    {
        $this->field = $field;
        $this->operator = $operator;

        if (is_null($value)) {
            $this->value = new ValueNull();
        } elseif (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = [$value];
        }

        if (is_null($strategyToSql)) {
            $this->strategyToSql = new FilterToWhereConditionSQL();
        } else {
            $this->strategyToSql = $strategyToSql;
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
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IFilter
    {
        $this->strategyToSql = $strategy;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStrategyToSql(): IStrategyToSQL
    {
        return $this->strategyToSql;
    }

    /**
     * @param IField $field
     * @param number|string|boolean $value
     * @return IFilter
     * @throws Exception
     */
    public static function eq(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value) || is_bool($value))) {
            throw new Exception(
                "Error in the Filter::eq(...) -> Error in type of value, the value must to be number or string or boolean."
            );
        }
        return new Filter($field, IFilter::EQUAL, $value);
    }

    /**
     * @param IField $field
     * @param number|string|boolean $value
     * @return IFilter
     * @throws Exception
     */
    public static function ne(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value) || is_bool($value))) {
            throw new Exception(
                "Error in the Filter::ne(...) -> Error in type of value, the value must to be number or string or boolean."
            );
        }
        return new Filter($field, IFilter::DISTINCT, $value);
    }

    /**
     * @param IField $field
     * @param number|string $value
     * @return IFilter
     * @throws Exception
     */
    public static function gte(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value))) {
            throw new Exception(
                "Error in the Filter::gte(...) -> Error in type of value, the value must to be number or string or boolean."
            );
        }
        return new Filter($field, IFilter::MAJOR_EQUAL, $value);
    }

    /**
     * @param IField $field
     * @param number|string $value
     * @return IFilter
     * @throws Exception
     */
    public static function gt(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value))) {
            throw new Exception(
                "Error in the Filter::gt(...) -> Error in type of value, the value must to be number or string."
            );
        }
        return new Filter($field, IFilter::MAJOR, $value);
    }

    /**
     * @param IField $field
     * @param number|string $value
     * @return IFilter
     * @throws Exception
     */
    public static function lte(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value))) {
            throw new Exception(
                "Error in the Filter::lte(...) -> Error in type of value, the value must to be number or string."
            );
        }
        return new Filter($field, IFilter::LESS_EQUAL, $value);
    }

    /**
     * @param IField $field
     * @param number|string $value
     * @return IFilter
     * @throws Exception
     */
    public static function lt(IField $field, $value): IFilter
    {
        if (!(is_numeric($value) || is_string($value))) {
            throw new Exception(
                "Error in the Filter::lt(...) -> Error in type of value, the value must to be number or string."
            );
        }
        return new Filter($field, IFilter::LESS, $value);
    }

    /**
     * @param IField $field
     * @return IFilter
     */
    public static function isNull(IField $field): IFilter
    {
        return new Filter($field, Filter::IS_NULL);
    }

    /**
     * @param IField $field
     * @return IFilter
     */
    public static function isNotNull(IField $field): IFilter
    {
        return new Filter($field, Filter::IS_NOT_NULL);
    }

    /**
     * @param IField $field
     * @param string[]|number[] $value
     * @return IFilter
     * @throws Exception
     */
    public static function between(IField $field, $value): IFilter
    {
        if (!is_array($value) || count($value) !== 2) {
            throw new Exception(
                "Error in the Filter::between(...) -> Error in type and length of value, the values must to be an Array and length === 2"
            );
        }
        return new Filter($field, IFilter::BETWEEN, $value);
    }

    /**
     * @param IField $field
     * @param string[]|number[] $value
     * @return IFilter
     * @throws Exception
     */
    public static function notBetween(IField $field, $value): IFilter
    {
        if (!is_array($value) || count($value) !== 2) {
            throw new Exception(
                "Error in the Filter::notBetween(...) -> Error in type and length of value, the values must to be an Array and length === 2"
            );
        }
        return new Filter($field, IFilter::NOT_BETWEEN, $value);
    }

    /**
     * @param IField $field
     * @param string[]|number[] $value
     * @return IFilter
     * @throws Exception
     */
    public static function in(IField $field, $value): IFilter
    {
        if (!is_array($value) || count($value) === 0) {
            throw new Exception(
                "Error in the Filter::in(...) -> Error in type and length of value, the values must to be an Array and length === 0"
            );
        }
        return new Filter($field, IFilter::IN, $value);
    }

    /**
     * @param IField $field
     * @param string[]|number[] $value
     * @return IFilter
     * @throws Exception
     */
    public static function notIn(IField $field, $value): IFilter
    {
        if (!is_array($value) || count($value) === 0) {
            throw new Exception(
                "Error in the Filter::notIn(...) -> Error in type and length of value, the values must to be an Array and length === 0"
            );
        }
        return new Filter($field, IFilter::NOT_IN, $value);
    }

    /**
     * @param IField $left
     * @param IField $right
     * @return IFilter
     */
    public static function column(IField $left, IField $right): IFilter
    {
        return new Filter($left, IFilter::COLUMN, $right);
    }

    /**
     * @return ISql
     */
    public function toSQL(): ISql
    {
        return $this->getStrategyToSql()->toSQL($this);
    }

    /**
     * @return ITable[]
     */
    public function getTable()
    {
        if ($this->getOperator() === IFilter::COLUMN) {
            return [
                $this->getField()->getTable(),
                $this->getValue()->getTable()
            ];
        }
        return [$this->getField()->getTable()];
    }
}