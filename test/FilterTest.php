<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;


use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\IField;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\FieldToSelectExprSQL;
use Cratia\ORM\DQL\Table;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class FilterTest
 * @package Tests\Cratia\ORM\DQL
 */
class FilterTest extends PHPUnit_TestCase
{
    public function testConstructorTypeColumnWithAs()
    {
        $tableSchema = 'userdata';
        $tableAs = 'user';
        $table = new Table($tableSchema, $tableAs);

        $fieldColumn = 'id';
        $fieldAs = 'register_id';
        $field = Field::column($table, $fieldColumn, $fieldAs);

        $sql = new Sql();
        $sql->sentence = "{$tableAs}.{$fieldColumn} AS {$fieldAs}";
        $sql->params = [];

        $this->assertInstanceOf(Field::class, $field);
        $this->assertInstanceOf(IField::class, $field);
        $this->assertInstanceOf(FieldToSelectExprSQL::class, $field->getStrategyToSql());
        $this->assertEquals($table, $field->getTable());
        $this->assertEquals(IField::COLUMN, $field->getType());
        $this->assertEquals($fieldColumn, $field->getColumn());
        $this->assertEquals($fieldAs, $field->getAs());
        $this->assertEquals($sql, $field->toSQL());
    }

    public function testConstructorTypeColumnWithoutAs()
    {
        $tableSchema = 'userdata';
        $tableAs = 'user';
        $table = new Table($tableSchema, $tableAs);

        $fieldColumn = 'id';
        $field = Field::column($table, $fieldColumn);

        $sql = new Sql();
        $sql->sentence = "{$tableAs}.{$fieldColumn} AS {$fieldColumn}";
        $sql->params = [];

        $this->assertInstanceOf(Field::class, $field);
        $this->assertInstanceOf(IField::class, $field);
        $this->assertInstanceOf(FieldToSelectExprSQL::class, $field->getStrategyToSql());
        $this->assertEquals($table, $field->getTable());
        $this->assertEquals(IField::COLUMN, $field->getType());
        $this->assertEquals($fieldColumn, $field->getColumn());
        $this->assertEquals($fieldColumn, $field->getAs());
        $this->assertEquals($sql, $field->toSQL());
    }

    public function testConstructorTypeCustom()
    {
        $tableSchema = 'userdata';
        $tableAs = 'user';
        $table = new Table($tableSchema, $tableAs);

        $fieldColumn = 'count(1)';
        $fieldAs = 'count';
        $field = Field::custom($table, $fieldColumn, $fieldAs);

        $sql = new Sql();
        $sql->sentence = "{$fieldColumn} AS {$fieldAs}";
        $sql->params = [];

        $this->assertInstanceOf(Field::class, $field);
        $this->assertInstanceOf(IField::class, $field);
        $this->assertInstanceOf(FieldToSelectExprSQL::class, $field->getStrategyToSql());
        $this->assertEquals($table, $field->getTable());
        $this->assertEquals(IField::CUSTOM, $field->getType());
        $this->assertEquals($fieldColumn, $field->getColumn());
        $this->assertEquals($fieldAs, $field->getAs());
        $this->assertEquals($sql, $field->toSQL());
    }

}