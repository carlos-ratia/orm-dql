<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\RelationToTableReferencesSql;

/**
 * Class Relation
 * @package Cratia\ORM\DQL
 */
class Relation implements IRelation
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var IField
     */
    private $left;

    /**
     * @var IField
     */
    private $right;

    /**
     * @var string
     */
    private $type;

    /**
     * @var FilterGroup
     */
    private $filters;

    /**
     * @var IStrategyToSQL
     */
    private $strategyToSQL;

    /**
     * Relation constructor.
     * @param IField $left
     * @param IField $right
     * @param string $type
     * @param IStrategyToSQL $strategyToSQL
     */
    private function __construct(IField $left, IField $right, string $type, IStrategyToSQL $strategyToSQL)
    {
        $this->id = $right->getTable()->getAs();
        $this->left = $left;
        $this->right = $right;
        $this->type = $type;
        $this->filters = FilterGroup::and();
        $this->strategyToSQL = $strategyToSQL;
    }

    /**
     * @param IField $left
     * @param IField $right
     * @param IStrategyToSQL|null $strategyToSQL
     * @return Relation
     */
    public static function inner(IField $left, IField $right, IStrategyToSQL $strategyToSQL = null)
    {
        if (is_null($strategyToSQL)) {
            $strategyToSQL = new RelationToTableReferencesSql();
        }
        return new Relation($left, $right, IRelation::INNER, $strategyToSQL);
    }

    /**
     * @param IField $left
     * @param IField $right
     * @param IStrategyToSQL|null $strategyToSQL
     * @return Relation
     */
    public static function left(IField $left, IField $right, IStrategyToSQL $strategyToSQL = null)
    {
        if (is_null($strategyToSQL)) {
            $strategyToSQL = new RelationToTableReferencesSql();
        }
        return new Relation($left, $right, IRelation::LEFT, $strategyToSQL);
    }

    /**
     * @param IField $left
     * @param IField $right
     * @param IStrategyToSQL|null $strategyToSQL
     * @return Relation
     */
    public static function right(IField $left, IField $right, IStrategyToSQL $strategyToSQL = null)
    {
        if (is_null($strategyToSQL)) {
            $strategyToSQL = new RelationToTableReferencesSql();
        }
        return new Relation($left, $right, IRelation::RIGHT, $strategyToSQL);
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getLeft(): IField
    {
        return $this->left;
    }

    /**
     * @inheritDoc
     */
    public function getRight(): IField
    {
        return $this->right;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): FilterGroup
    {
        return $this->filters;
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
    public function setStrategyToSQL(IStrategyToSQL $strategyToSQL): IRelation
    {
        $this->strategyToSQL = $strategyToSQL;
        return $this;
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
    public function getTable(): ITable
    {
        return $this->getRight()->getTable();
    }

    /**
     * @inheritDoc
     */
    public function addFilter(IFilter $filter): IRelation
    {
        $this->getFilters()->add($filter);
        return $this;
    }
}