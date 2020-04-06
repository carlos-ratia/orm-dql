<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\FieldNull;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterNull;
use Cratia\ORM\DQL\GroupByNull;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IGroupBy;
use Cratia\ORM\DQL\Interfaces\IOrderBy;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\OrderByNull;
use Cratia\ORM\DQL\RelationNull;
use Cratia\ORM\DQL\SubQuery;
use Cratia\ORM\Strategies\SQL\MySQL\QueryRelationBag;

/**
 * Class QueryParts
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryParts
{
    /**
     * @var bool
     */
    private $foundRows;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var string[]
     */
    private $groupBys;

    /**
     * @var string[]
     */
    private $orderBys;

    /**
     * @var string
     */
    private $from;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string[]
     */
    private $joins;

    /**
     * @var string[]
     */
    private $where;

    /**
     * @var array
     */
    private $params;

    /**
     * @var QueryRelations
     */
    private $relations;

    /**
     * @var IQuery[]
     */
    private $sub_querys;

    /**
     * QueryParts constructor.
     * @param IQuery $query
     */
    public function __construct(IQuery $query)
    {
        $this->foundRows = $query->getFoundRows();
        $this->fields = [];
        $this->from = $query
            ->getFrom()
            ->setStrategyToSQL(new TableToSQL())
            ->toSQL()
            ->getSentence();
        $this->joins = [];
        $this->where = [];
        $this->orderBys = [];
        $this->groupBys = [];
        $this->limit = $query->getLimit();
        $this->offset = $query->getOffset();
        $this->params = [];
        $this->relations = QueryRelations::create($query);
        $this->sub_querys = [];

        $this->loadRelationsForQuery($query);   //FIRST STEP
        $this->loadSqlFieldsForQuery($query);   //FIELDS
        $this->loadSqlJoinsForQuery($query);    //JOINS
        $this->loadSqlSubQuerysForQuery($query);    //SUB-QUERYS
        $this->loadSqlFiltersForQuery($query);  //WHERES
        $this->loadSqlGroupBysForQuery($query); //GROUP BYS
        $this->loadSqlOrderBysForQuery($query); //ORDER BYS
    }

    /**
     * @param IQuery $query
     */
    protected function loadRelationsForQuery(IQuery $query): void
    {
        //FIELDS
        /** @var IField $field */
        foreach ($query->getFields() as $field) {
            if ($field instanceof FieldNull) {
                continue;
            }
            $this->getRelations()->setAsRequired($field->getTable());
        }

        //FILTERS
        /** @var IFilter $filter */
        foreach ($query->getFilters() as $filter) {
            if ($filter instanceof FilterNull) {
                continue;
            }
            /** @var ITable $table */
            foreach ($filter->getTable() as $table) {
                $this->getRelations()->setAsRequired($table);
            }
        }

        // GROUPS BY
        /** @var IGroupBy $groupBy */
        foreach ($query->getGroupBys() as $groupBy) {
            if ($groupBy instanceof GroupByNull) {
                continue;
            }
            /** @var ITable $table */
            foreach ($groupBy->getTable() as $table) {
                $this->getRelations()->setAsRequired($table);
            }
        }

        // ORDERS BY
        /** @var IOrderBy $orderBy */
        foreach ($query->getOrderBys() as $orderBy) {
            if ($orderBy instanceof OrderByNull) {
                continue;
            }
            /** @var ITable $table */
            foreach ($orderBy->getTable() as $table) {
                $this->getRelations()->setAsRequired($table);
            }
        }
    }

    /**
     * @param IQuery $query
     */
    protected function loadSqlFieldsForQuery(IQuery $query): void
    {
        /** @var IField $field */
        foreach ($query->getFields() as $field) {
            if ($field instanceof FieldNull) {
                continue;
            }
            /** @var ISql $sql */
            $sql = $field->setStrategyToSQL(new FieldToSelectExprSQL())->toSQL();
            $this->fields[] = $sql->getSentence();
            $this->addParams($sql->getParams());
        }
    }

    /**
     * @param IQuery $query
     */
    protected function loadSqlJoinsForQuery(IQuery $query): void
    {
        /** @var IRelation $relation */
        foreach ($query->getRelations() as $relation) {
            if ($relation instanceof RelationNull) {
                continue;
            }
            if (!$this->getRelations()->isRequired($relation)) {
                continue;
            }
            if (
                ($bag = $this->getRelations()->get($relation)) !== false &&
                ($bag instanceof QueryRelationBag)
            ) {
                $sql = $bag->getSql();
                $this->joins[] = $sql->getSentence();
                $this->addParams($sql->getParams());
            }
        }
    }


    /**
     * @param IQuery $query
     */
    protected function loadSqlSubQuerysForQuery(IQuery $query)
    {
        foreach ($query->getSubQuerys() as $sub_query) {
            /** @var SubQuery $sub_query */
            $response = $sub_query->toSQL();
            $this->sub_querys[] = "{$sub_query->getJoinType()} JOIN ({$response->getSentence()}) as {$sub_query->getAs()->getAs()}";
            $this->addParams($response->getParams());
        }
    }

    /**
     * @param IQuery $query
     */
    protected function loadSqlFiltersForQuery(IQuery $query): void
    {
        /** @var IFilter $filter */
        foreach ($query->getFilters() as $filter) {
            if ($filter instanceof FilterNull) {
                continue;
            }
            /** @var ISql $sql */
            if ($filter instanceof Filter) {
                $sql = $filter->setStrategyToSQL(new FilterToWhereConditionSQL())->toSQL();
            } else {
                $sql = $filter->setStrategyToSQL(new FilterGroupToWhereConditionSQL())->toSQL();
            }
            $this->where[] = $sql->getSentence();
            $this->addParams($sql->getParams());
        }
    }

    /**
     * @param IQuery $query
     */
    protected function loadSqlGroupBysForQuery(IQuery $query): void
    {
        /** @var IGroupBy $groupBy */
        foreach ($query->getGroupBys() as $groupBy) {
            if ($groupBy instanceof GroupByNull) {
                continue;
            }
            /** @var ISql $sql */
            $sql = $groupBy->setStrategyToSQL(new GroupByToSql())->toSQL();
            $this->groupBys[] = $sql->getSentence();
            $this->addParams($sql->getParams());
        }
    }

    /**
     * @param IQuery $query
     */
    protected function loadSqlOrderBysForQuery(IQuery $query): void
    {
        /** @var IOrderBy $orderBy */
        foreach ($query->getOrderBys() as $orderBy) {
            if ($orderBy instanceof OrderByNull) {
                continue;
            }
            /** @var ISql $sql */
            $sql = $orderBy->setStrategyToSQL(new OrderByToSQL())->toSQL();
            $this->orderBys[] = $sql->getSentence();
            $this->addParams($sql->getParams());
        }
    }

    /**
     * @return bool
     */
    public function isFoundRows(): bool
    {
        return $this->foundRows;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string[]
     */
    public function getGroupBys(): array
    {
        return $this->groupBys;
    }

    /**
     * @return string[]
     */
    public function getOrderBys(): array
    {
        return $this->orderBys;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return string[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return string[]
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return QueryRelations
     */
    public function getRelations(): QueryRelations
    {
        return $this->relations;
    }

    /**
     * @return IQuery[]
     */
    public function getSubQuerys(): array
    {
        return $this->sub_querys;
    }



    /**
     * @return bool
     */
    public function hasFields(): bool
    {
        return (is_array($this->getFields()) && count($this->getFields()) > 0);
    }

    /**
     * @return bool
     */
    public function hasJoins(): bool
    {
        return (is_array($this->getJoins()) && count($this->getJoins()) > 0);
    }

    /**
     * @return bool
     */
    public function hasQuerys(): bool
    {
        return (is_array($this->getSubQuerys()) && count($this->getSubQuerys()) > 0);
    }

    /**
     * @return bool
     */
    public function hasWhere(): bool
    {
        return (is_array($this->getWhere()) && count($this->getWhere()) > 0);
    }

    /**
     * @return bool
     */
    public function hasGroupBys(): bool
    {
        return (is_array($this->getGroupBys()) && count($this->getGroupBys()) > 0);
    }

    /**
     * @return bool
     */
    public function hasOrderBys(): bool
    {
        return (is_array($this->getOrderBys()) && count($this->getOrderBys()) > 0);
    }

    /**
     * @param array $params
     * @return QueryParts
     */
    protected function addParams(array $params): QueryParts
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }
}