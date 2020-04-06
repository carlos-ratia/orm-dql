<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

use Cratia\ORM\DQL\SubQuery;

/**
 * Interface IQuery
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IQuery
{
    /**
     * @var int
     */
    const LIMIT = 20;

    /**
     * @var int
     */
    const NO_LIMIT = -1;

    /**
     * @return ITable
     */
    public function getFrom(): ITable;

    /**
     * @param IStrategyToSQL $strategy
     * @return IQuery
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IQuery;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @return bool
     */
    public function getFoundRows(): bool;

    /**
     * @param bool $foundRows
     * @return IQuery
     */
    public function setFoundRows(bool $foundRows): IQuery;

    /**
     * @return IRelation[]
     */
    public function getRelations();

    /**
     * @return IField[]
     */
    public function getFields();

    /**
     * @return IFilter[]
     */
    public function getFilters();

    /**
     * @return IGroupBy[]
     */
    public function getGroupBys();

    /**
     * @return IOrderBy[]
     */
    public function getOrderBys();

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @param IField $field
     * @return IQuery
     */
    public function addField(IField $field): IQuery;

    /**
     * @param int $limit
     * @return IQuery
     */
    public function setLimit(int $limit): IQuery;

    /**
     * @param int $offset
     * @return IQuery
     */
    public function setOffset(int $offset): IQuery;

    /**
     * @param IFilter $filter
     * @return IQuery
     */
    public function addFilter(IFilter $filter): IQuery;

    /**
     * @param IGroupBy $groupBy
     * @return IQuery
     */
    public function addGroupBy(IGroupBy $groupBy): IQuery;

    /**
     * @param IOrderBy $orderBy
     * @return IQuery
     */
    public function addOrderBy(IOrderBy $orderBy): IQuery;

    /**
     * @param IQuery $query
     * @return IQuery
     */
    public function join(IQuery $query): IQuery;

    /**
     * @return IQuery[]
     */
    public function getSubQuerys(): array;

    /**
     * @param SubQuery $query
     * @return IQuery
     */
    public function addSubQuery(SubQuery $query): IQuery;
}