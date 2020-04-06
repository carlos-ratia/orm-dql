<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IGroupBy;
use Cratia\ORM\DQL\Interfaces\IOrderBy;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryToSQL;

/**
 * Class Query
 * @package Cratia\ORM\DQL
 */
class Query implements IQuery
{
    /**
     * @var bool
     */
    private $foundRows;

    /**
     * @var IField[]
     */
    private $fields;

    /**
     * @var IFilter[]
     */
    private $filters;

    /**
     * @var IGroupBy[]
     */
    private $groupBys;

    /**
     * @var IOrderBy[]
     */
    private $orderBys;

    /**
     * @var ITable
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
     * @var IStrategyToSQL
     */
    private $strategyToSQL;

    /**
     * @var IQuery[]
     */
    private $sub_querys;


    /**
     * Query constructor.
     * @param ITable|null $from
     * @param IStrategyToSQL|null $strategy
     */
    public function __construct(ITable $from = null, IStrategyToSQL $strategy = null)
    {
        $this->foundRows = true;
        $this->limit = IQuery::LIMIT;
        $this->offset = 0;
        if (is_null($from)) {
            $this->from = new TableNull();
        } else {
            $this->from = $from;
        }
        if (is_null($strategy)) {
            $this->strategyToSQL = new QueryToSQL();
        } else {
            $this->strategyToSQL = $strategy;
        }
        $this->fields = [];
        $this->filters = [];
        $this->groupBys = [];
        $this->orderBys = [];
        $this->sub_querys = [];
    }

    /**
     * @inheritDoc
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IQuery
    {
        $this->strategyToSQL = $strategyToSQL;
        return $this;
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
    public function toSQL(): ISql
    {
        return $this->getStrategyToSQL()->toSQL($this);
    }

    /**
     * @inheritDoc
     */
    public function getFoundRows(): bool
    {
        return $this->foundRows;
    }

    /**
     * @inheritDoc
     */
    public function setFoundRows(bool $foundRows): IQuery
    {
        $this->foundRows = $foundRows;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRelations()
    {
        return $this->getFrom()->getRelations();
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function getGroupBys()
    {
        return $this->groupBys;
    }

    /**
     * @inheritDoc
     */
    public function getOrderBys()
    {
        return $this->orderBys;
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): ITable
    {
        return $this->from;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function setLimit(int $limit): IQuery
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): IQuery
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(IField $field): IQuery
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFilter(IFilter $filter): IQuery
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addGroupBy(IGroupBy $groupBy): IQuery
    {
        $this->groupBys[] = $groupBy;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addOrderBy(IOrderBy $orderBy): IQuery
    {
        $this->orderBys[] = $orderBy;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function join(IQuery $query): IQuery
    {
        /** @var IField $filter */
        foreach ($query->getFields() as $field) {
            $this->addField($field);
        }
        /** @var IFilter $filter */
        foreach ($query->getFilters() as $filter) {
            $this->addFilter($filter);
        }
        /** @var IGroupBy $group_by */
        foreach ($query->getGroupBys() as $group_by) {
            $this->addGroupBy($group_by);
        }
        /** @var IOrderBy $order_by */
        foreach ($query->getOrderBys() as $order_by) {
            $this->addOrderBy($order_by);
        }

        $this
            ->setLimit($query->getLimit())
            ->setOffset($query->getOffset());
        return $this;
    }

    public function addSubQuery(SubQuery $query): IQuery
    {
        if (is_null($query->getAs())) {
            throw new \Exception("The querys to do a join need to have an As setted");
        }
        $this->sub_querys[] = $query;
        return $this;
    }

    public function getSubQuerys(): array
    {
        return $this->sub_querys;
    }


}