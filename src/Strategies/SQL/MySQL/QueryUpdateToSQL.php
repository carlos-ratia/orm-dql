<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IFieldValue;
use Cratia\ORM\DQL\Interfaces\IQueryUpdate;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use DateTime;
use Exception;

/**
 * Class QueryUpdateToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryUpdateToSQL implements IStrategyToSQL
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IQueryUpdate)) {
            $method = __METHOD__;
            throw new Exception("Error in the {$method}(...) -> The reference is not instance of IQueryUpdate.");
        }

        /** @var IQueryUpdate $reference */
        $sql = new Sql();
        $sql->sentence = $this->getSQLSentence($reference);
        $sql->params = $this->getSQLParams($reference);
        return $sql;
    }

    /**
     * @param IQueryUpdate $reference
     * @return string
     */
    protected function getSQLSentence(IQueryUpdate $reference): string
    {
        /** @var IFieldValue[] $fields */
        $fields = $reference->getFields();

        $sql_columns = array_map(function (IFieldValue $field) {
            $sentence = $field
                ->getField()
                ->setStrategyToSQL(new FieldToWhereConditionSQL())
                ->toSql()
                ->getSentence();
            return "{$sentence} = ?";
        }, $fields);
        $sql_columns = implode(',', $sql_columns);

        return "UPDATE {$reference->getFrom()->toSQL()->getSentence()} SET {$sql_columns} WHERE {$reference->getFilter()->toSQL()->getSentence()}";
    }

    /**
     * @param IQueryUpdate $reference
     * @return array
     */
    protected function getSQLParams(IQueryUpdate $reference): array
    {
        /** @var IFieldValue[] $fields */
        $fields = $reference->getFields();

        $sql_params = [];

        /** @var IFieldValue $field */
        foreach ($fields as $field) {
            $value = $field->getValue();
            if (
                is_string($value) &&
                (
                    (DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false) ||
                    (DateTime::createFromFormat('Y-m-d', $value) !== false))
            ) {
                $sql_value = "{$value}";
            } elseif (is_string($value)) {
                $sql_value = "{$value}";
            } elseif (is_numeric($value)) {
                $sql_value = $value;
            } elseif (is_bool($value)) {
                $sql_value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } elseif (is_null($value)) {
                $sql_value = null;
            } else {
                $sql_value = $value;
            }
            $sql_params[] = $sql_value;
        }

        return array_merge($reference->getFrom()->toSQL()->getParams(), $sql_params, $reference->getFilter()->toSQL()->getParams());
    }
}