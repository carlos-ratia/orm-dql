<?php
declare(strict_types=1);

namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterQueryToWhereConditionSQL;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FilterToWhereConditionSQL;

class FilterQuery implements IFilter
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
     * @var Query
     */
    private $value;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSql;

    /**
     * FilterQuery constructor.
     * @param IField $field
     * @param string $operator
     * @param Query $value
     * @param IStrategyToSQL $strategyToSql
     */
    public function __construct(IField $field, string $operator, Query $value, IStrategyToSQL $strategyToSql = null)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $this->checkQuery($value);

        if (is_null($strategyToSql)) {
            $this->strategyToSql = new FilterQueryToWhereConditionSQL();
        } else {
            $this->strategyToSql = $strategyToSql;
        }
    }

    /**
     * @param Query $query
     * @return Query
     * @throws \Exception
     */
    private function checkQuery(Query $query):Query
    {
        $fields_count = count($query->getFields());
        if ($fields_count > 1) {
            throw new \Exception("Error in the FilterQuery::__constructor() -> The query on a FilterQuery cannot have more than 1 field, {$fields_count} founded");
        }
        /** @var IField $field */
        $field = current($query->getFields());
        if ($field->getType() == IField::TABLE) {
            throw new \Exception("Error in the FilterQuery::__constructor() -> The query on a FilterQuery cannot have TABLE (all_column_schema) field ");
        }

        $query->setLimit(Query::NO_LIMIT);
        $query->setFoundRows(false);
        return $query;
    }

    /**
     * @return IField
     */
    public function getField(): IField
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return Query
     */
    public function getValue(): Query
    {
        return $this->value;
    }

    /**
     * @return array|ITable[]
     */
    public function getTable()
    {
        return [$this->getField()->getTable()];
    }

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSql(): IStrategyToSQL
    {
        return $this->strategyToSql;
    }

    /**
     * @param IStrategyToSQL $strategyToSql
     * @return FilterQuery
     */
    public function setStrategyToSql(IStrategyToSQL $strategyToSql): IFilter
    {
        $this->strategyToSql = $strategyToSql;
        return $this;
    }


    public function toSQL(): ISql
    {
        return $this->getStrategyToSql()->toSQL($this);
    }


}