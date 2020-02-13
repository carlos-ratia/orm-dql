<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class TableToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class TableToSQL implements IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof ITable)) {
            throw new Exception("Error in the TableToSQL::toSQL(...) -> The reference is not instance of ITable.");
        }
        /** @var ITable $reference */

        $sql = new Sql();
        $sql->sentence = $this->getSentence($reference);
        $sql->params = [];
        return $sql;
    }

    /**
     * @param ITable $table
     * @return string
     */
    private function getSentence(ITable $table)
    {
        if ($table->getTableSchema() === $table->getAs()) {
            return "{$table->getTableSchema()}";
        } else {
            return "{$table->getTableSchema()} AS {$table->getAs()}";
        }
    }
}