<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IRelation;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class RelationToTableReferencesSql
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class RelationToTableReferencesSql implements IStrategyToSQL
{

    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IRelation)) {
            throw new Exception("Error in the FilterGroupToWhereConditionSQL::toSQL(...) -> The reference is not instance of IRelation.");
        }
        /** @var IRelation $reference */


        $type = $reference->getType();

        $tableRight = $reference
            ->getRight()
            ->getTable()
            ->setStrategyToSql(new TableToSQL())->toSQL()
            ->getSentence();

        $left = $reference
            ->getLeft()
            ->setStrategyToSQL(new FieldToWhereConditionSQL())
            ->toSQL()
            ->getSentence();

        $right = $reference
            ->getRight()
            ->setStrategyToSQL(new FieldToWhereConditionSQL())
            ->toSQL()
            ->getSentence();

        /** @var ISql $filterSQL */
        $filterSQL = $reference->getFilters()->toSQL();

        $sql = new Sql();
        $sql->params = $filterSQL->getParams();

        $sql->sentence = "{$type} JOIN {$tableRight} ON {$left} = {$right}";
        $sql->sentence .= (is_string($filterSQL->getSentence()) && !empty($filterSQL->getSentence()))
            ? " AND {$filterSQL->getSentence()}"
            : "";

        return $sql;
    }
}