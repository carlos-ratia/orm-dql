<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface IFieldValue
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IFieldValue
{
    /**
     * @return ITable
     */
    public function getTable(): ITable;

    /**
     * @return IField
     */
    public function getField(): IField;

    /**
     * @return string
     */
    public function getColumn(): string;

    /**
     * @return mixed
     */
    public function getValue();
}