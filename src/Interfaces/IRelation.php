<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

use Cratia\ORM\DQL\FilterGroup;

/**
 * Interface IRelation
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IRelation
{
    const LEFT = "LEFT";
    const INNER = "INNER";
    const RIGHT = "RIGHT";

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return IField
     */
    public function getLeft(): IField;

    /**
     * @return IField
     */
    public function getRight(): IField;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return FilterGroup
     */
    public function getFilters(): FilterGroup;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @param IStrategyToSQL $strategyToSQL
     * @return IRelation
     */
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IRelation;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;

    /**
     * @return ITable
     */
    public function getTable(): ITable;

    /**
     * @param IFilter $filter
     * @return IRelation
     */
    public function addFilter(IFilter $filter): IRelation;
}