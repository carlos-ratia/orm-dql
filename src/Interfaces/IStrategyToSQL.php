<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface IStrategyToSQL
 * @package Cratia\ORM\DQL\Interfaces
 */
interface IStrategyToSQL
{
    /**
     * @param $reference
     * @return ISql
     */
    public function toSQL($reference): ISql;
}