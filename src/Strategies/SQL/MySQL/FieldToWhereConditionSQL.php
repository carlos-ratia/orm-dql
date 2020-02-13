<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Strategies\SQL\MySQL;


use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\Interfaces\IStrategyToSQL;
use Cratia\ORM\DQL\Sql;
use Exception;

/**
 * Class FieldToWhereConditionSQL
 * @package Cratia\ORM\DQL\Strategies\SQL\MySQL
 */
class FieldToWhereConditionSQL implements IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     * @throws Exception
     */
    public function toSQL($reference): ISql
    {
        if (!($reference instanceof IField)) {
            throw new Exception("Error in the FieldToWhereConditionSQL::toSQL(...) -> The reference is not instance of IField.");
        }
        /** @var IField $reference */

        $sql = new Sql();
        $sql->sentence = $this->getSentence($reference);
        $sql->params = [];
        return $sql;
    }

    /**
     * @param IField $field
     * @return string
     * @throws Exception
     */
    protected function getSentence(IField $field)
    {
        switch ($field->getType()) {
            case IField::COLUMN:
                return "{$field->getTable()->getAs()}.{$field->getColumn()}";

            case IField::CUSTOM:
                return "{$field->getColumn()}";

            default:
                throw new Exception(
                    "Error in the FieldToWhereConditionSQL::getSentence(...field) -> The field->getType(): string is not valid."
                );
        }
    }
}
