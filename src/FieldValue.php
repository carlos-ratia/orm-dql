<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;

use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Interfaces\IFieldValue;
use Cratia\ORM\DQL\Interfaces\ITable;

/**
 * Class FieldValue
 * @package Cratia\ORM\DQL
 */
class FieldValue implements IFieldValue
{
    /**
     * @var IField
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * FieldValue constructor.
     * @param IField $field
     * @param $value
     */
    public function __construct(IField $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return IField
     */
    public function getField(): IField
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function getTable(): ITable
    {
        return $this->getField()->getTable();
    }

    /**
     * @inheritDoc
     */
    public function getColumn(): string
    {
        return $this->getField()->getColumn();
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }
}