<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\ISql;

/**
 * Class Sql
 * @package Cratia\ORM\DQL
 */
class Sql implements ISql
{
    /**
     * @var string
     */
    public $sentence;

    /**
     * @var array
     */
    public $params;

    /**
     * Sql constructor.
     */
    public function __construct()
    {
        $this->sentence = '';
        $this->params = [];
    }

    /**
     * @return string
     */
    public function getSentence(): string
    {
        return $this->sentence;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}