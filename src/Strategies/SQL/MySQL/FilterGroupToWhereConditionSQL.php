<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class FilterGroupToWhereConditionSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class FilterGroupToWhereConditionSQL implements IStrategyToSQL
{

    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof FilterGroup)) {
            throw new Exception("Error in the FilterGroupToWhereConditionSQL::toSQL(...) -> The reference is not instance of FilterGroup.");
        }
        /** @var FilterGroup $reference */

        $sql = new Sql();
        $sql->sentence = $this->getSentence($reference);
        $sql->params = $this->getParams($reference);
        return $sql;
    }

    /**
     * @param FilterGroup $filterGroup
     * @return string
     */
    protected function getSentence(FilterGroup $filterGroup)
    {
        if ($filterGroup->isEmpty()) {
            return "";
        }
        $glue = $filterGroup->getMode()
            ? " AND "
            : " OR ";
        /** @var string[] $subSentence */
        $subSentence = [];
        /** @var IFilter $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $subSentence[] = $filter->toSQL()->getSentence();
        }
        $sentence = '(' . implode($glue, $subSentence) . ')';
        return $sentence;
    }

    /**
     * @param FilterGroup $filterGroup
     * @return array|string
     */
    protected function getParams(FilterGroup $filterGroup)
    {
        if ($filterGroup->isEmpty()) {
            return [];
        }
        /** @var string[] $subSentence */
        $params = [];
        /** @var IFilter $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $params = array_merge($params, $filter->toSQL()->getParams());
        }

        return $params;
    }
}