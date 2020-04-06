<?php


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;

class SubQuery extends Query
{


    /**
     * Only required if query is going to be a sub-query
     * @var Table
     */
    private $as;

    private $join_type;

    /**
     * SubQuery constructor.
     * @param ITable $as Name that the sun query going to have as a SubQuery
     * @param ITable|null $from
     * @param IStrategyToSQL|null $strategy
     */
    public function __construct(ITable $as, ITable $from = null, IStrategyToSQL $strategy = null)
    {
        parent::__construct($from, $strategy);
        $this->setFoundRows(false);
        $this->join_type = "";
        $this->as = $as;
    }

    /**
     * @return string
     */
    public function getJoinType(): string
    {
        return $this->join_type;
    }

    /**
     * @param string $join_type
     * @return SubQuery
     */
    public function setJoinType(string $join_type): SubQuery
    {
        $this->join_type = $join_type;
        return $this;
    }

    /**
     * @return Table
     */
    public function getAs(): Table
    {
        return $this->as;
    }

    /**
     * @param Table $as
     * @return SubQuery
     */
    public function setAs(Table $as): SubQuery
    {
        $this->as = $as;
        return $this;
    }

}