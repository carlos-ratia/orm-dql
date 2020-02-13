<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface IField
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IField
{
    const COLUMN = 'column_schema';
    const CUSTOM = 'custom_column_schema';
    const CONSTANT = 'constant';
    const TABLE = 'all_column_schema';
    const CALLBACK = 'function';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return ITable
     */
    public function getTable(): ITable;

    /**
     * @return string
     */
    public function getColumn(): string;

    /**
     * @return string
     */
    public function getAs(): string;

    /**
     * @return callable
     */
    public function getCallback(): callable;

    /**
     * @param IStrategyToSQL $strategy
     * @return IField
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IField;

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
    public function isCallback(): bool;
}