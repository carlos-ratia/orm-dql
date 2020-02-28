<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;

use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\QueryInsert;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryInsertToSQL;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class QueryInsertTest
 * @package Tests\Cratia\ORM\DQL
 */
class QueryInsertTest extends PHPUnit_TestCase
{
    public function testSimpleInsert1()
    {
        $table = new Table("table_1", "t1");
        $insert = new QueryInsert($table, new QueryInsertToSQL());

        $this->assertInstanceOf(Table::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());
    }

    public function testSimpleInsert2()
    {
        $insert = new QueryInsert();

        $this->assertInstanceOf(TableNull::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());
    }

    public function testSimpleInsert3()
    {
        $insert = new QueryInsert();

        $this->assertInstanceOf(TableNull::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());

        $insert->setStrategyToSQL(new QueryInsertToSQL());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());

        $insert->setFrom(new Table("table_1", "t1"));
        $this->assertInstanceOf(Table::class, $insert->getFrom());

    }


    public function testSimpleInsert4()
    {
        $table = new Table("table_1", "t1");
        $insert = new QueryInsert();

        $this->assertInstanceOf(TableNull::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());
        $this->assertEmpty($insert->getFields());

        $insert->setStrategyToSQL(new QueryInsertToSQL());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());

        $insert->setFrom($table);
        $this->assertInstanceOf(Table::class, $insert->getFrom());

        $insert->addField(Field::column($table, 'column1'), 1);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $insert->addField(Field::column($table, 'column21'), false);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $insert->addField(Field::column($table, 'column22'), true);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $insert->addField(Field::column($table, 'column3', 'column3_as'), null);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $insert->addField(Field::column($table, 'column4'), '1');
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $date1 = date('Y-m-d');
        $insert->addField(Field::column($table, 'column5'), $date1);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $date2 = date('Y-m-d H:i:s');
        $insert->addField(Field::column($table, 'column6'), $date2);
        $this->assertIsArray($insert->getFields());
        $this->assertNotEmpty($insert->getFields());

        $sql = $insert->toSql();
        $this->assertInstanceOf(ISql::class, $sql);
        $sentence = "INSERT INTO `table_1` (`column1`,`column21`,`column22`,`column3`,`column4`,`column5`,`column6`) VALUES (?,?,?,?,?,?,?)";
        $param = [1, 0, 1, null, '1', "{$date1}", "{$date2}"];

        $this->assertEquals($sentence, $sql->getSentence());
        $this->assertEquals($param, $sql->getParams());

    }

    public function testSimpleInsert5()
    {
        $table1 = new Table("table_1", "t1");
        $table2 = new Table("table_2", "t2");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\DQL\QueryInsert::addField(....,1) -> The field must contain the same insert table, Insert table ({$table1->getTableSchema()}) Field table ({$table2->getTableSchema()}).");
        $this->expectExceptionCode(0);

        $insert = new QueryInsert();

        $this->assertInstanceOf(TableNull::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());
        $this->assertEmpty($insert->getFields());

        $insert->setStrategyToSQL(new QueryInsertToSQL());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());

        $insert->setFrom($table1);
        $this->assertInstanceOf(Table::class, $insert->getFrom());

        $insert->addField(Field::column($table2, 'column1'), 1);

    }

    public function testSimpleInsert6()
    {
        $table1 = new Table("table_1", "t1");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\DQL\QueryInsert::addField(...,1) -> The field must be of the type column_schema.");
        $this->expectExceptionCode(0);

        $insert = new QueryInsert();

        $this->assertInstanceOf(TableNull::class, $insert->getFrom());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());
        $this->assertIsArray($insert->getFields());
        $this->assertEmpty($insert->getFields());

        $insert->setStrategyToSQL(new QueryInsertToSQL());
        $this->assertInstanceOf(QueryInsertToSQL::class, $insert->getStrategyToSQL());

        $insert->setFrom($table1);
        $this->assertInstanceOf(Table::class, $insert->getFrom());

        $insert->addField(Field::custom($table1, 'column1', 'column1_as'), 1);

    }
}