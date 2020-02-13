<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\Mysql\FieldToSelectExprSQL;
use Exception;

/**
 * Class Field
 * @package Cratia\ORM\DQL
 */
class Field implements IField
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var ITable
     */
    private $table;

    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $as;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var FieldToSelectExprSQL
     */
    private $strategyToSql;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return ITable
     */
    public function getTable(): ITable
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getAs(): string
    {
        return $this->as;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Field constructor.
     * @param string $type
     * @param ITable $table
     * @param string $column
     * @param string $as
     * @param IStrategyToSQL|null $strategyToSql
     */
    private function __construct(
        string $type,
        ITable $table,
        string $column,
        string $as,
        IStrategyToSQL $strategyToSql = null
    )
    {
        $this->type = $type;
        $this->table = $table;
        $this->column = $column;
        $this->as = $as;
        $this->callback = null;

        if (!is_null($strategyToSql)) {
            $this->strategyToSql = $strategyToSql;
        } else {
            $this->strategyToSql = new FieldToSelectExprSQL();
        }
    }

    /**
     * @param ITable $table
     * @param string $column
     * @param string|null $as
     * @return IField
     */
    public static function column(
        ITable $table,
        string $column,
        string $as = null
    ): IField
    {
        if (is_null($as) || empty($as)) {
            $as = $column;
        }
        return new Field(self::COLUMN, $table, $column, $as);
    }

    /**
     * @param ITable $table
     * @param string $column
     * @param string $as
     * @return IField
     */
    public static function custom(
        ITable $table,
        string $column,
        string $as
    ): IField
    {
        return new Field(self::CUSTOM, $table, $column, $as);
    }

    /**
     * @param string $column
     * @param string $as
     * @return IField
     */
    public static function constant(
        string $column,
        string $as
    ): IField
    {
        return new Field(self::CONSTANT, new TableNull(), $column, $as);
    }

    /**
     * @param ITable $table
     * @return IField
     */
    public static function table(
        ITable $table
    ): IField
    {
        return new Field(self::TABLE, $table, '*', '');
    }

    /**
     * @param callable $fn
     * @param string $as
     * @return IField
     */
    public static function callback(
        callable $fn,
        string $as
    ): IField
    {
        $field = new Field(self::CALLBACK, new TableNull(), "", $as);
        $field->callback = $fn;
        return $field;
    }

    /**
     * @param IStrategyToSQL $strategy
     * @return IField
     */
    public function setStrategyToSQL(IStrategyToSQL $strategy): IField
    {
        $this->strategyToSql = $strategy;
        return $this;
    }

    /**
     * @return IStrategyToSQL
     */
    public function getStrategyToSQL(): IStrategyToSQL
    {
        return $this->strategyToSql;
    }

    /**
     * @return ISql
     * @throws Exception
     */
    public function toSQL(): ISql
    {
        return $this->getStrategyToSQL()->toSQL($this);
    }

    /**
     * @return bool
     */
    public function isCallback(): bool
    {
        return $this->getType() === self::CALLBACK &&
            !is_null($this->getCallback()) &&
            is_callable($this->getCallback());
    }
}