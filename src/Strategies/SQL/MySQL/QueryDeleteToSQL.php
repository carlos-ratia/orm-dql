<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IQueryDelete;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class QueryDeleteToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryDeleteToSQL implements IStrategyToSQL
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IQueryDelete)) {
            $method = __METHOD__;
            throw new Exception("Error in the {$method}(...) -> The reference is not instance of IQueryDelete.");
        }

        /** @var IQueryDelete $reference */
        $sql = new Sql();
        $sql->sentence = $this->getSQLSentence($reference);
        $sql->params = $this->getSQLParams($reference);
        return $sql;
    }

    /**
     * @param IQueryDelete $reference
     * @return string
     */
    protected function getSQLSentence(IQueryDelete $reference): string
    {
        return "DELETE {$reference->getFrom()->getAs()} FROM {$reference->getFrom()->toSQL()->getSentence()} WHERE {$reference->getFilter()->toSQL()->getSentence()}";
    }

    /**
     * @param IQueryDelete $reference
     * @return array
     */
    protected function getSQLParams(IQueryDelete $reference): array
    {
        return array_merge($reference->getFrom()->toSQL()->getParams(), $reference->getFilter()->toSQL()->getParams());
    }
}