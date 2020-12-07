<?php


namespace Tests\Cratia\ORM\DQL;

use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\GroupBy;
use Cratia\ORM\DQL\Having;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Relation;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\Table;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class QueryHavingTest
 * @package Tests\Cratia\ORM\DQL
 */
class QueryHavingTest extends PHPUnit_TestCase
{
    /**
     * @throws Exception
     */
    public function testQuery1()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");
        $field13 = Field::column($table1, "last_name");
        $field14 = Field::column($table1, "allow_brand");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");
        $field23 = Field::column($table2, "last_name");
        $field24 = Field::column($table2, "allow_newsletters");

        $table3 = new Table("brand_table_1", "bt1");
        $field30 = Field::column($table3, "id", "brand_consumer_id");
        $field31 = Field::column($table3, "allow_brand", "brand_consumer_id");

        $table1
            ->addRelation(Relation::inner($field10, $field20))
            ->addRelation(
                Relation::inner($field11, $field30)
                    ->addFilter(Filter::eq($field31, true))
            );

        $query = new Query($table1);
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField($field20)
            ->addField($field30)
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::notIn($field23, ["ratia", "viloria"]))
                    ->add(Filter::ne($field24, false))
            )
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::notIn($field13, ["ratia", "viloria"]))
                    ->add(Filter::isNotNull($field14))
            )
            ->addGroupBy(GroupBy::create($field10))
            ->addGroupBy(GroupBy::create($field20))
            ->addGroupBy(GroupBy::create($field30))
            ->setHaving(
                Having::create()
                    ->addCondition(Filter::notIn($field13, ["ratia", "viloria"]))
                    ->addCondition(Filter::isNotNull($field14))
            )
            ->setLimit(23)
            ->setOffset(17);

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, t2.id AS consumer_id, bt1.id AS brand_consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id INNER JOIN brand_table_1 AS bt1 ON t1.id_brand_table_1 = bt1.id AND (bt1.allow_brand = ?) WHERE (t2.last_name NOT IN (?,?) OR t2.allow_newsletters != ?) AND (t1.last_name NOT IN (?,?) OR t1.allow_brand IS NOT NULL) GROUP BY t1.id_consumer, t2.id, bt1.id HAVING (t1.last_name NOT IN (?,?) AND t1.allow_brand IS NOT NULL) LIMIT 23 OFFSET 17";
        $sql->params = [1, "ratia", "viloria", 0, "ratia", "viloria", "ratia", "viloria"];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }
}