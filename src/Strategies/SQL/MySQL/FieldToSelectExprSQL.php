<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\Mysql;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class FieldToSelectExprSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\Mysql
 */
class FieldToSelectExprSQL implements IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IField)) {
            throw new Exception("Error in the FieldToSelectExprSQL::toSQL(...) -> The reference is not instance of IField.");
        }
        /** @var IField $reference */
        return $this->getSentence($reference);
    }

    /**
     * @param IField $field
     * @return Sql
     * @throws Exception
     */
    protected function getSentence(IField $field)
    {
        $sql = new Sql();
        switch ($field->getType()) {
            case IField::COLUMN:
                $sql->sentence = "{$field->getTable()->getAs()}.{$field->getColumn()} AS {$field->getAs()}";
                $sql->params = [];
                break;

            case IField::CUSTOM:
                $sql->sentence = "{$field->getColumn()} AS {$field->getAs()}";
                $sql->params = [];
                break;

            case IField::TABLE:
                $sql->sentence = "{$field->getTable()->getAs()}.*";
                $sql->params = [];
                break;

            case IField::CALLBACK:
                $sql->sentence = "'CALLBACK' AS {$field->getAs()}";
                $sql->params = [];
                break;

            case IField::CONSTANT:
                if (is_numeric($field->getColumn())) {
                    $sql->sentence = "{$field->getColumn()} AS {$field->getAs()}";
                    $sql->params = [];
                } else {
                    $sql->sentence = "'{$field->getColumn()}' AS {$field->getAs()}";
                    $sql->params = [];
                }
                break;

            default:
                throw new Exception("Error in the FieldToSelectExprSQL::toSQL(...reference}) -> The field->getType(): string ({$field->getType()}) is not valid.");

        }

        return $sql;
    }
}