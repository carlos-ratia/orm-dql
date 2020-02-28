<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;

use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\QueryUpdate;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryUpdateToSQL;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class QueryUpdateTest
 * @package Tests\Cratia\ORM\DQL
 */
class QueryUpdateTest extends PHPUnit_TestCase
{
    public function testSimpleUpdate1()
    {
        $table = new Table("table_1", "t1");
        $update = new QueryUpdate($table, new QueryUpdateToSQL());

        $this->assertInstanceOf(Table::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertInstanceOf(IFilter::class, $update->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $update->getFilter());
        $this->assertIsArray($update->getFields());

    }

    public function testSimpleUpdate2()
    {
        $update = new QueryUpdate();

        $this->assertInstanceOf(TableNull::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertInstanceOf(IFilter::class, $update->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $update->getFilter());
        $this->assertIsArray($update->getFields());
    }

    public function testSimpleUpdate3()
    {
        $update = new QueryUpdate();

        $this->assertInstanceOf(TableNull::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertIsArray($update->getFields());

        $update->setStrategyToSQL(new QueryUpdateToSQL());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());

        $update->setFrom(new Table("table_1", "t1"));
        $this->assertInstanceOf(Table::class, $update->getFrom());

        $this->assertInstanceOf(IFilter::class, $update->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $update->getFilter());

    }


    public function testSimpleUpdate4()
    {
        $table = new Table("table_1", "t1");
        $update = new QueryUpdate();

        $this->assertInstanceOf(TableNull::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertIsArray($update->getFields());
        $this->assertEmpty($update->getFields());

        $update->setStrategyToSQL(new QueryUpdateToSQL());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());

        $update->setFrom($table);
        $this->assertInstanceOf(Table::class, $update->getFrom());

        $update->addField(Field::column($table, 'column1'), 1);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $update->addField(Field::column($table, 'column21'), false);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $update->addField(Field::column($table, 'column22'), true);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $update->addField(Field::column($table, 'column3', 'column3_as'), null);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $update->addField(Field::column($table, 'column4'), '1');
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $date1 = date('Y-m-d');
        $update->addField(Field::column($table, 'column5'), $date1);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $date2 = date('Y-m-d H:i:s');
        $update->addField(Field::column($table, 'column6'), $date2);
        $this->assertIsArray($update->getFields());
        $this->assertNotEmpty($update->getFields());

        $update->addFilter(Filter::eq(Field::column($table, 'column1'), 1));
        $update->addFilter(Filter::eq(Field::column($table, 'column2'), false));
        $update->addFilter(Filter::eq(Field::column($table, 'column3'), true));

        $sql = $update->toSql();
        $this->assertInstanceOf(ISql::class, $sql);
        $sentence = "UPDATE `table_1 AS t1` SET `t1.column1` = ?,`t1.column21` = ?,`t1.column22` = ?,`t1.column3` = ?,`t1.column4` = ?,`t1.column5` = ?,`t1.column6` = ? WHERE (t1.column1 = ? AND t1.column2 = ? AND t1.column3 = ?)";
        $param = [1, 0, 1, null, '1', "{$date1}", "{$date2}", 1, 0, 1];

        $this->assertEquals($sentence, $sql->getSentence());
        $this->assertEquals($param, $sql->getParams());

    }

    public function testSimpleUpdate5()
    {
        $table1 = new Table("table_1", "t1");
        $table2 = new Table("table_2", "t2");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\DQL\QueryUpdate::addField(....,1) -> The field must contain the same insert table, Insert table ({$table1->getTableSchema()}) Field table ({$table2->getTableSchema()}).");
        $this->expectExceptionCode(0);

        $update = new QueryUpdate();

        $this->assertInstanceOf(TableNull::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertIsArray($update->getFields());
        $this->assertEmpty($update->getFields());

        $update->setStrategyToSQL(new QueryUpdateToSQL());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());

        $update->setFrom($table1);
        $this->assertInstanceOf(Table::class, $update->getFrom());

        $update->addField(Field::column($table2, 'column1'), 1);

    }

    public function testSimpleUpdate6()
    {
        $table1 = new Table("table_1", "t1");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error in the Cratia\ORM\DQL\QueryUpdate::addField(...,1) -> The field must be of the type column_schema.");
        $this->expectExceptionCode(0);

        $update = new QueryUpdate();

        $this->assertInstanceOf(TableNull::class, $update->getFrom());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());
        $this->assertIsArray($update->getFields());
        $this->assertEmpty($update->getFields());

        $update->setStrategyToSQL(new QueryUpdateToSQL());
        $this->assertInstanceOf(QueryUpdateToSQL::class, $update->getStrategyToSQL());

        $update->setFrom($table1);
        $this->assertInstanceOf(Table::class, $update->getFrom());

        $update->addField(Field::custom($table1, 'column1', 'column1_as'), 1);

    }
}