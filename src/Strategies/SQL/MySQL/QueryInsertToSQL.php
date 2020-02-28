<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IQueryInsert;
use Cratia\ORM\DQL\Interfaces\IFieldValue;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use DateTime;
use Exception;

/**
 * Class InsertToSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class QueryInsertToSQL implements IStrategyToSQL
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IQueryInsert)) {
            $method = __METHOD__;
            throw new Exception("Error in the {$method}(...) -> The reference is not instance of IQueryInsert.");
        }

        /** @var IQueryInsert $reference */
        $sql = new Sql();
        $sql->sentence = $this->getSQLSentence($reference);
        $sql->params = $this->getSQLParams($reference);
        return $sql;
    }

    /**
     * @param IQueryInsert $reference
     * @return string
     */
    protected function getSQLSentence(IQueryInsert $reference): string
    {
        /** @var IFieldValue[] $fields */
        $fields = $reference->getFields();

        $sql_columns = array_map(function (IFieldValue $field) {
            return "`{$field->getColumn()}`";
        }, $fields);
        $sql_columns = implode(',', $sql_columns);

        $sql_values = array_map(function () {
            return "?";
        }, $fields);
        $sql_values = implode(',', $sql_values);

        return "INSERT INTO `{$reference->getFrom()->getTableSchema()}` ({$sql_columns}) VALUES ({$sql_values})";
    }

    /**
     * @param IQueryInsert $reference
     * @return array
     */
    protected function getSQLParams(IQueryInsert $reference): array
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
                $sql_value = "'{$value}'";
            } elseif (is_string($value)) {
                $sql_value = "{$value}";
            } elseif (is_numeric($value)) {
                $sql_value = $value;
            } elseif (is_bool($value)) {
                $sql_value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            } elseif (is_null($value)) {
                $sql_value = 'NULL';
            } else {
                $sql_value = $value;
            }
            $sql_params[] = $sql_value;
        }

        return $sql_params;
    }
}