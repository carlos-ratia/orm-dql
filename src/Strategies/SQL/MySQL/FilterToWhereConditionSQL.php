<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\ValueNull;
use Exception;

/**
 * Class FilterToWhereConditionSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class FilterToWhereConditionSQL implements IStrategyToSQL
{
    /**
     * @var array
     */
    protected $mapOperatorInSql =
        [
            IFilter::EQUAL => "=",
            IFilter::MAJOR_EQUAL => ">=",
            IFilter::MAJOR => ">",
            IFilter::LESS_EQUAL => "<=",
            IFilter::LESS => "<",
            IFilter::DISTINCT => "!=",
            IFilter::BETWEEN => false,
            IFilter::NOT_BETWEEN => false,
            IFilter::IN => false,
            IFilter::NOT_IN => false,
            IFilter::IS_NULL => "IS NULL",
            IFilter::IS_NOT_NULL => "IS NOT NULL",
            IFilter::COLUMN => false,
            IFilter::CONTAIN => false,
            IFilter::START_WITH => false,
            IFilter::END_WITH => false,
            IFilter::NOT_CONTAIN => false,
            IFilter::NOT_START_WITH => false,
            IFilter::NOT_END_WITH => false,
        ];

    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IFilter)) {
            throw new Exception("Error in the FilterToWhereConditionSQL::toSQL(...) -> The reference is not instance of IFilter.");
        }
        /** @var IFilter $reference */

        $sql = new Sql();
        $sql->sentence = $this->getSentence($reference);
        $sql->params = $this->getParams($reference);
        return $sql;
    }

    /**
     * @param IFilter $filter
     * @return string
     * @throws Exception
     */
    protected function getSentence(IFilter $filter)
    {
        /** @var IField $field */
        $field = $filter->getField()->setStrategyToSQL(new FieldToWhereConditionSQL());

        /** @var string $fieldSQL */
        $fieldSQL = $field->toSQL()->getSentence();

        /** @var string|false $operator */
        $operator = $this->getOperatorToSQL($filter);

        /** @var IField|ValueNull|mixed $value */
        $value = $filter->getValue();

        if ($value instanceof ValueNull) {
            return "{$fieldSQL} {$operator}";
        }
        if ($operator===false) {
            switch ($filter->getOperator()) {
                case  IFilter::BETWEEN:
                    return "{$fieldSQL} BETWEEN ? AND ?";
                    break;
                case  IFilter::NOT_BETWEEN:
                    return "{$fieldSQL} NOT BETWEEN ? AND ?";
                    break;
                case  IFilter::IN:
                    return "{$fieldSQL} IN ({$this->getSentenceForInOrNotIn($filter)})";
                    break;
                case  IFilter::NOT_IN:
                    return "{$fieldSQL} NOT IN ({$this->getSentenceForInOrNotIn($filter)})";
                    break;
                case  IFilter::COLUMN:
                    /** @var IField $fieldLeft */
                    $fieldLeft = $filter->getField()->setStrategyToSQL(new FieldToWhereConditionSQL());
                    /** @var IField $fieldRight */
                    $fieldRight = $filter->getValue()->setStrategyToSQL(new FieldToWhereConditionSQL());
                    return "{$fieldLeft->toSQL()} = {$fieldRight->toSQL()}";
                    break;
                case  IFilter::CONTAIN:
                case  IFilter::START_WITH:
                case  IFilter::END_WITH:
                    return "{$fieldSQL} LIKE ?";
                    break;
                case  IFilter::NOT_CONTAIN:
                case  IFilter::NOT_START_WITH:
                case  IFilter::NOT_END_WITH:
                    return "{$fieldSQL} NOT LIKE ?";
                    break;
            }
        } else {
            if ($value instanceof IField) {
                $field = $filter->getValue()->setStrategyToSQL(new FieldToWhereConditionSQL());
                return "{$fieldSQL} {$operator} {$field->toSQL()->getSentence()}";
            } else {
                return "{$fieldSQL} {$operator} ?";
            }
        }
    }


    /**
     * @param IFilter $filter
     * @return false|string
     * @throws Exception
     */
    protected function getOperatorToSQL(IFilter $filter)
    {
        if (!isset($this->mapOperatorInSql[$filter->getOperator()])) {
            throw new Exception(
                "Error in the FilterToWhereConditionSQL::getOperatorToSQL(...filter) -> The operator ({$filter->getOperator()}) is not defined in the Map Operator to SQL."
            );
        }
        return $this->mapOperatorInSql[$filter->getOperator()];
    }

    /**
     * @param IFilter $filter
     * @return string
     * @throws Exception
     */
    protected function getSentenceForInOrNotIn(IFilter $filter)
    {
        $values = $filter->getValue();
        if (!is_array($values)) {
            throw new Exception(
                "Error in the FilterToWhereConditionSQL::getSentenceForInOrNotIn(...filter) -> The operator ({$filter->getOperator()}) needs the values to be of the array type."
            );
        }
        return implode(',', array_fill(0, count($values), '?'));
    }

    protected function getParams(IFilter $filter)
    {
        $result = [];
        $value = $filter->getValue();
        if ($value instanceof ValueNull) {
            return [];
        } elseif ($value instanceof IField) {
            return [];
        } elseif (is_array($value) && count($value) === 0) {
            return [];
        } elseif (is_array($value) && count($value) > 0) {
            foreach ($value as $item) {
                if ($item instanceof ValueNull) {
                    continue;
                }
                if (is_bool($item)) {
                    $item = (int)$item;
                }
                $result[] = $item;
            }
            return $this->addWildCardsToParams($filter->getOperator(),$result);
        } elseif (!is_array($value)) {
            return $this->addWildCardsToParams($filter->getOperator(),[$value]);
        } else {
            return $this->addWildCardsToParams($filter->getOperator(),array_merge([], $value));
        }
    }

    protected function addWildCardsToParams($operator, $value)
    {
        array_walk($value, function (&$val) use ($operator) {
            switch ($operator) {
                case  IFilter::CONTAIN:
                case  IFilter::NOT_CONTAIN:
                    $val = "%{$val}%";
                    break;
                case  IFilter::START_WITH:
                case  IFilter::NOT_START_WITH:
                    $val = "{$val}%";
                    break;
                case  IFilter::END_WITH:
                case  IFilter::NOT_END_WITH:
                    $val = "%{$val}";
                    break;
            }
        });
        return $value;

    }
}