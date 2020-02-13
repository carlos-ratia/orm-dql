<?php
declare(strict_types=1);


namespace Cratia\ORM\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;

/**
 * Class QueryRelationBag
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryRelationBag
{
    /**
     * @var bool
     */
    private $required;
    /**
     * @var IRelation
     */
    private $relation;

    /**
     * @var ISql
     */
    private $sql;

    /**
     * QueryRelationBag constructor.
     * @param IRelation $relation
     * @param bool $required
     */
    public function __construct(IRelation $relation, bool $required)
    {
        $this->required = $required;
        $this->relation = $relation;
        $this->sql = $relation->toSQL();
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return IRelation
     */
    public function getRelation(): IRelation
    {
        return $this->relation;
    }

    /**
     * @return ISql
     */
    public function getSql(): ISql
    {
        return $this->sql;
    }

}