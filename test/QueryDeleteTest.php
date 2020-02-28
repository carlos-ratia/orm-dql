<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\DQL;

use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\ISql;
use Cratia\ORM\DQL\QueryDelete;
use Cratia\ORM\DQL\Strategies\SQL\MySQL\QueryDeleteToSQL;
use Cratia\ORM\DQL\Table;
use Cratia\ORM\DQL\TableNull;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class QueryDeleteTest
 * @package Tests\Cratia\ORM\DQL
 */
class QueryDeleteTest extends PHPUnit_TestCase
{
    public function testSimpleDelete1()
    {
        $table = new Table("table_1", "t1");
        $delete = new QueryDelete($table, new QueryDeleteToSQL());

        $this->assertInstanceOf(Table::class, $delete->getFrom());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());
        $this->assertInstanceOf(IFilter::class, $delete->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $delete->getFilter());

    }

    public function testSimpleDelete2()
    {
        $delete = new QueryDelete();

        $this->assertInstanceOf(TableNull::class, $delete->getFrom());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());
        $this->assertInstanceOf(IFilter::class, $delete->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $delete->getFilter());

    }

    public function testSimpleDelete3()
    {
        $delete = new QueryDelete();

        $this->assertInstanceOf(TableNull::class, $delete->getFrom());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());


        $delete->setStrategyToSQL(new QueryDeleteToSQL());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());

        $delete->setFrom(new Table("table_1", "t1"));
        $this->assertInstanceOf(Table::class, $delete->getFrom());

        $this->assertInstanceOf(IFilter::class, $delete->getFilter());
        $this->assertInstanceOf(FilterGroup::class, $delete->getFilter());

    }


    public function testSimpleDelete4()
    {
        $table = new Table("table_1", "t1");
        $delete = new QueryDelete();

        $this->assertInstanceOf(TableNull::class, $delete->getFrom());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());


        $delete->setStrategyToSQL(new QueryDeleteToSQL());
        $this->assertInstanceOf(QueryDeleteToSQL::class, $delete->getStrategyToSQL());

        $delete->setFrom($table);
        $this->assertInstanceOf(Table::class, $delete->getFrom());

        $date1 = date('Y-m-d');
        $date2 = date('Y-m-d H:i:s');
        $delete->addFilter(Filter::eq(Field::column($table, 'column1'), 1));
        $delete->addFilter(Filter::eq(Field::column($table, 'column2'), false));
        $delete->addFilter(Filter::eq(Field::column($table, 'column3'), true));
        $delete->addFilter(Filter::eq(Field::column($table, 'column3'), $date1));
        $delete->addFilter(Filter::eq(Field::column($table, 'column3'), $date2));

        $sql = $delete->toSql();
        $this->assertInstanceOf(ISql::class, $sql);
        $sentence = "DELETE t1 FROM table_1 AS t1 WHERE (t1.column1 = ? AND t1.column2 = ? AND t1.column3 = ? AND t1.column3 = ? AND t1.column3 = ?)";
        $param = [1, 0, 1, "{$date1}", "{$date2}"];

        $this->assertEquals($sentence, $sql->getSentence());
        $this->assertEquals($param, $sql->getParams());

    }
}