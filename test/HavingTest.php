<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\Having;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\Table;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class HavingTest
 * @package Tests\Cratia\ORM\DQL
 */
class HavingTest extends PHPUnit_TestCase
{
    public function testConstructorHaving()
    {
        $tableSchema = 'userdata';
        $tableAs = 'user';
        $table = new Table($tableSchema, $tableAs);

        $filter = Filter::eq(Field::column($table, 'id'), 0);

        $having = Having::create();
        $having->addCondition($filter);

        $sql = new Sql();
        $sql->sentence = "({$filter->toSQL()->getSentence()})";
        $sql->params = [0];

        $this->assertEquals($sql->getSentence(), $having->toSQL()->getSentence());
        $this->assertEquals($sql->getParams(), $having->toSQL()->getParams());
    }
}