<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class QueryToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryToSQL implements IStrategyToSQL
{

    /**
     * @var IQuery
     */
    private $query;

    /**
     * @var QueryParts
     */
    private $queryParts;

    /**
     * @return IQuery
     */
    public function getQuery(): ?IQuery
    {
        return $this->query;
    }

    /**
     * @return QueryParts
     */
    public function getQueryParts(): ?QueryParts
    {
        return $this->queryParts;
    }

    /**
     * @param QueryParts $queryParts
     * @return QueryToSQL
     */
    public function setQueryParts(QueryParts $queryParts): QueryToSQL
    {
        $this->queryParts = $queryParts;
        return $this;
    }



    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IQuery)) {
            throw new Exception("Error in the QueryToSQL::toSQL(...) -> The reference is not instance of IQuery.");
        }

        /** @var IQuery $reference */
        $this->query = $reference;
        if(is_null($this->queryParts)){
            $this->queryParts = new QueryParts($reference);
        }


        $sql = new Sql();
        $sql->sentence = $this->getSQLSentence();
        $sql->params = $this->getSQLParams();
        return $sql;
    }

    /**
     * @return string
     */
    private function getSQLSentence()
    {
        $sqlFoundRows = $this->getQueryParts()->isFoundRows();

        //FIELDS
        if ($this->getQueryParts()->hasFields()) {
            $sqlFieldsQuery = implode(', ', $this->getQueryParts()->getFields());
        } else {
            $sqlFieldsQuery = "{$this->getQuery()->getFrom()->getAs()}.*";
        }

        //FROM
        $sqlFrom = $this->getQueryParts()->getFrom();

        //JOINS
        $sqlJoinQuery = false;
        if ($this->getQueryParts()->hasJoins()) {
            $sqlJoinQuery = implode(' ', $this->getQueryParts()->getJoins());
        }

        //WHERE
        $sqlWhereQuery = false;
        if ($this->getQueryParts()->hasWhere()) {
            $sqlWhereQuery = implode(' AND ', $this->getQueryParts()->getWhere());
        }

        //GROUP BYS
        $sqlGroupBys = false;
        if ($this->getQueryParts()->hasGroupBys()) {
            $sqlGroupBys = implode(', ', $this->getQueryParts()->getGroupBys());
        }

        //ORDER BYS
        $sqlOrderBys = false;
        if ($this->getQueryParts()->hasOrderBys()) {
            $sqlOrderBys = implode(', ', $this->getQueryParts()->getOrderBys());
        }

        $sql = (!$sqlFoundRows)
            ? "SELECT"
            : "SELECT SQL_CALC_FOUND_ROWS";
        $sql = "{$sql} {$sqlFieldsQuery}";        //FIELDS
        $sql = "{$sql} FROM {$sqlFrom}";          //FROM
        $sql = (!$sqlJoinQuery)                   //JOIN
            ? $sql
            : "{$sql} {$sqlJoinQuery}";
        $sql = (!$sqlWhereQuery)                  //WHERE
            ? $sql
            : "{$sql} WHERE {$sqlWhereQuery}";
        $sql = (!$sqlGroupBys)                    //GROUP BY
            ? $sql
            : "{$sql} GROUP BY {$sqlGroupBys}";
        $sql = (!$sqlOrderBys)                    //ORDER BY
            ? $sql
            : "{$sql} ORDER BY {$sqlOrderBys}";
        $sql = ($this->getQueryParts()->getLimit() === IQuery::NO_LIMIT)
            ? $sql
            : "{$sql} LIMIT {$this->getQueryParts()->getLimit()} OFFSET {$this->getQueryParts()->getOffset()}";

        return trim($sql);
    }

    /**
     * @return array
     */
    protected function getSQLParams(): array
    {
        return $this->getQueryParts()->getParams();
    }
}