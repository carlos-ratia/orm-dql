<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

use Cratia\ORM\DQL\ValueNull;

/**
 * Interface IFilter
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IFilter
{
    const EQUAL = "==";
    const MAJOR_EQUAL = ">=";
    const MAJOR = ">";
    const LESS_EQUAL = "<=";
    const LESS = "<";
    const DISTINCT = "!=";
    const BETWEEN = "~";
    const NOT_BETWEEN = "!~";
    const IN = "^";
    const NOT_IN = "!^";
    const IS_NULL = "IS NULL";
    const IS_NOT_NULL = "IS NOT NULL";
    const COLUMN = "=^";

    const CONTAIN = "@@";
    const START_WITH = "=@";
    const END_WITH = "@=";
    const NOT_CONTAIN = "!@@";
    const NOT_START_WITH = "!=@";
    const NOT_END_WITH = "!@=";

    const MODE_INCLUSIVE = true;
    const MODE_EXCLUSIVE = false;

    /**
     * @return IField
     */
    public function getField(): IField;

    /**
     * @return string
     */
    public function getOperator(): string;

    /**
     * @return IField|ValueNull|number|boolean|string|string[]|number[]|mixed[]
     */
    public function getValue();

    /**
     * @return ITable[]
     */
    public function getTable();

    /**
     * @param IStrategyToSQL $strategy
     * @return IFilter
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IFilter;

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL;

    /**
     * @return ISql
     */
    public function toSQL(): ISql;
}