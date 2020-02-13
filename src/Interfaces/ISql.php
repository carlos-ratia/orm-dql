<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL\Interfaces;

/**
 * Interface ISql
 * @package Cratia\ORM\DQL\Interfaces
 */
interface ISql
{
    /**
     * @return string
     */
    public function getSentence(): string;

    /**
     * @return array
     */
    public function getParams(): array;
}