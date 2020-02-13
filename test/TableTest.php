<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\ITable;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\TableToSQL;
use Cratia\ORM\DQL\Table;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class TableTest
 * @package Tests\Cratia\ORM\DQL
 */
class TableTest extends PHPUnit_TestCase
{
    public function testConstructorTableSchemaEqualAs()
    {
        $tableSchema = 'test';
        $as = 'test';
        $table = new Table($tableSchema, $as);

        $sql = new Sql();
        $sql->sentence = $tableSchema;
        $sql->params = [];

        $this->assertInstanceOf(Table::class, $table);
        $this->assertInstanceOf(ITable::class, $table);
        $this->assertInstanceOf(TableToSQL::class, $table->getStrategyToSql());
        $this->assertEquals($tableSchema, $table->getTableSchema());
        $this->assertEquals($as, $table->getAs());
        $this->assertEquals($sql, $table->toSQL());

    }

    public function testConstructorTableSchemaNoEqualAs()
    {
        $tableSchema = 'userdata';
        $as = 'user';
        $table = new Table($tableSchema, $as);

        $sql = new Sql();
        $sql->sentence = "{$tableSchema} AS {$as}";
        $sql->params = [];

        $this->assertInstanceOf(Table::class, $table);
        $this->assertInstanceOf(ITable::class, $table);
        $this->assertInstanceOf(TableToSQL::class, $table->getStrategyToSql());
        $this->assertEquals($tableSchema, $table->getTableSchema());
        $this->assertEquals($as, $table->getAs());
        $this->assertEquals($sql, $table->toSQL());
    }
}