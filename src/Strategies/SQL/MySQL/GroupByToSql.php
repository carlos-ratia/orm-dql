<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IGroupBy;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Exception;

/**
 * Class GroupByToSql
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class GroupByToSql implements IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IGroupBy)) {
            throw new Exception("Error in the GroupByToSql::toSQL(...) -> The reference is not instance of IGroupBy.");
        }
        /** @var IFilter $reference */

        return $reference
            ->getField()
            ->setStrategyToSQL(new FieldToWhereConditionSQL())
            ->toSQL();
    }
}