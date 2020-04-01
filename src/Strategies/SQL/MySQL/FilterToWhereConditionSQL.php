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

        if ($filter->getOperator() === IFilter::BETWEEN) {
            return "{$fieldSQL} BETWEEN ? AND ?";
        } elseif ($filter->getOperator() === IFilter::NOT_BETWEEN) {
            return "{$fieldSQL} NOT BETWEEN ? AND ?";
        } elseif ($filter->getOperator() === IFilter::IN) {
            return "{$fieldSQL} IN ({$this->getSentenceForInOrNotIn($filter)})";
        } elseif ($filter->getOperator() === IFilter::NOT_IN) {
            return "{$fieldSQL} NOT IN ({$this->getSentenceForInOrNotIn($filter)})";
        } elseif ($filter->getOperator() === IFilter::COLUMN) {
            /** @var IField $fieldLeft */
            $fieldLeft = $filter->getField()->setStrategyToSQL(new FieldToWhereConditionSQL());
            /** @var IField $fieldRight */
            $fieldRight = $filter->getValue()->setStrategyToSQL(new FieldToWhereConditionSQL());
            return "{$fieldLeft->toSQL()} = {$fieldRight->toSQL()}";
        } else {
            if($value instanceof IField){
                $field = $filter->getValue()->setStrategyToSQL(new FieldToWhereConditionSQL());
                return "{$fieldSQL} {$operator} {$field->toSQL()->getSentence()}";
            }else{
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

        $filterMap = array_map(function () {
            return '?';
        }, $values);

        return implode(',', $filterMap);
    }

    protected function getParams(IFilter $filter)
    {
        $result = [];
        $value = $filter->getValue();
        if ($value instanceof ValueNull) {
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
            return $result;
        } elseif (!is_array($value)) {
            return [$value];
        } else {
            return array_merge([], $value);
        }
    }
}