<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IOrderBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class OrderByToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class OrderByToSQL implements IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IOrderBy)) {
            throw new Exception("Error in the OrderByToSQL::toSQL(...) -> The reference is not instance of IOrderBy.");
        }
        /** @var IOrderBy $reference */

        /** @var ISql $sqlBase */
        $sqlBase = $reference->getField()->setStrategyToSQL(new FieldToWhereConditionSQL())->toSQL();

        $sql = new Sql();
        $sql->sentence = "{$sqlBase->getSentence()} {$reference->getModeSorting()}";
        $sql->params = $sqlBase->getParams();
        return $sql;
    }
}