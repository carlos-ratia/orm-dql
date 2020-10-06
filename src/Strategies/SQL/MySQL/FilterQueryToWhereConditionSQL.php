<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\FilterQuery;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Exception;

class FilterQueryToWhereConditionSQL extends FilterToWhereConditionSQL
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
            IFilter::IN => false,
            IFilter::NOT_IN => false
        ];

    protected function getSentence(IFilter $filter)
    {
        /** @var FilterQuery $filter */


        /** @var string $fieldSQL */
        $fieldSQL = $filter->getField()->setStrategyToSQL(new FieldToWhereConditionSQL())->toSQL()->getSentence();

        /** @var string|false $operator */
        $operator = $this->getOperatorToSQL($filter);

        $sub_sentence = $filter->getValue()->toSQL()->getSentence();

        if ($operator===false) {
            switch ($filter->getOperator()) {
                case  IFilter::IN:
                    return "{$fieldSQL} IN ({$sub_sentence})";
                    break;
                case  IFilter::NOT_IN:
                    return "{$fieldSQL} NOT IN ({$sub_sentence})";
                    break;
            }
        }else{
            return "{$fieldSQL} {$operator} ({$sub_sentence})";
        }
    }

    protected function getParams(IFilter $filter)
    {
        return $filter->getValue()->toSQL()->getParams();
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
                "Error in the FilterQueryToWhereConditionSQL::getOperatorToSQL(...filter) -> The operator ({$filter->getOperator()}) is not valid in the Map Operator to SQL."
            );
        }
        return $this->mapOperatorInSql[$filter->getOperator()];
    }


}