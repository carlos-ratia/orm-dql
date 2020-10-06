<?php


namespace Tests\Cratia\ORM\DQL;


use App\Context\GeneralContext;
use Cratia\ORM\DQL\Field;
use Cratia\ORM\DQL\Filter;
use Cratia\ORM\DQL\FilterGroup;
use Cratia\ORM\DQL\FilterQuery;
use Cratia\ORM\DQL\GroupBy;
use Cratia\ORM\DQL\Interfaces\IFilter;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\DQL\OrderBy;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Relation;
use Cratia\ORM\DQL\Sql;
use Cratia\ORM\DQL\QuerySource;
use Cratia\ORM\DQL\Table;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Class QueryTest
 * @package Tests\Cratia\ORM\DQL
 */
class QueryTest extends PHPUnit_TestCase
{
    /**
     * @throws Exception
     */
    public function testSimpleQuery1()
    {
        $table = new Table("table_1", "t1");
        $query = new Query($table);
        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1 LIMIT 20 OFFSET 0";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testSimpleQuery2()
    {
        $table = new Table("table_1", "t1");
        $query = new Query($table);
        $query->setLimit(IQuery::NO_LIMIT);

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testSimpleQuery3()
    {
        $table = new Table("table_1", "t1");
        $query = new Query($table);
        $query
            ->addField(Field::column($table, "id", "id_as"))
            ->addField(Field::column($table, "first_name", "first_name_as"))
            ->addFilter(Filter::eq(Field::column($table, 'id'), 0))
            ->setLimit(20)
            ->setOffset(10);

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id AS id_as, t1.first_name AS first_name_as FROM table_1 AS t1 WHERE t1.id = ? LIMIT 20 OFFSET 10";
        $sql->params = [0];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testSimpleQuery4()
    {
        $table = new Table("table_2", "t2");
        $query = new Query($table);
        $query
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::gte(Field::column($table, "id"), 1))
                    ->add(Filter::isNull(Field::column($table, "first_name")))
            )
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::notIn(Field::column($table, "last_name"), ["ratia", "viloria"]))
                    ->add(Filter::ne(Field::column($table, "allow_newsletters"), false))
            );

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t2.* FROM table_2 AS t2 WHERE (t2.id >= ? OR t2.first_name IS NULL) AND (t2.last_name NOT IN (?,?) OR t2.allow_newsletters != ?) LIMIT 20 OFFSET 0";
        $sql->params = [1, 'ratia', 'viloria', 0];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery1()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");
        $field21 = Field::column($table2, "id");
        $field22 = Field::column($table2, "first_name");
        $field23 = Field::column($table2, "last_name");
        $field24 = Field::column($table2, "allow_newsletters");

        $table1->addRelation(Relation::inner($field10, $field20));

        $query = new Query($table1);
        $query
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::gte($field21, 1))
                    ->add(Filter::isNull($field22))
            )
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::notIn($field23, ["ratia", "viloria"]))
                    ->add(Filter::ne($field24, false))
            );

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id WHERE (t2.id >= ? OR t2.first_name IS NULL) AND (t2.last_name NOT IN (?,?) OR t2.allow_newsletters != ?) LIMIT 20 OFFSET 0";
        $sql->params = [1, 'ratia', 'viloria', 0];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery2()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");

        $table1
            ->addRelation(Relation::inner($field10, $field20));

        $query = new Query($table1);
        $query
            ->addField($field10)
            ->addField($field20)
            ->setLimit(IQuery::NO_LIMIT);

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t2.id AS consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery3()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");

        $table3 = new Table("brand_table_1", "bt1");
        $field30 = Field::column($table3, "id", "brand_consumer_id");

        $table1
            ->addRelation(Relation::inner($field10, $field20))
            ->addRelation(Relation::inner($field11, $field30));

        $query = new Query($table1);
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField($field20)
            ->addField($field30)
            ->setLimit(IQuery::LIMIT)
            ->setOffset(10);

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, t2.id AS consumer_id, bt1.id AS brand_consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id INNER JOIN brand_table_1 AS bt1 ON t1.id_brand_table_1 = bt1.id LIMIT 20 OFFSET 10";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery4()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");

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
            ->setLimit(IQuery::LIMIT)
            ->setOffset(10);

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, t2.id AS consumer_id, bt1.id AS brand_consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id INNER JOIN brand_table_1 AS bt1 ON t1.id_brand_table_1 = bt1.id AND (bt1.allow_brand = ?) LIMIT 20 OFFSET 10";
        $sql->params = [1];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery5()
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
            ->setLimit(23)
            ->setOffset(17);

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, t2.id AS consumer_id, bt1.id AS brand_consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id INNER JOIN brand_table_1 AS bt1 ON t1.id_brand_table_1 = bt1.id AND (bt1.allow_brand = ?) WHERE (t2.last_name NOT IN (?,?) OR t2.allow_newsletters != ?) AND (t1.last_name NOT IN (?,?) OR t1.allow_brand IS NOT NULL) GROUP BY t1.id_consumer, t2.id, bt1.id LIMIT 23 OFFSET 17";
        $sql->params = [1, "ratia", "viloria", 0, "ratia", "viloria"];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery6()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");

        $query = new Query($table1);
        $query
            ->addOrderBy(OrderBy::asc($field10));

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1 ORDER BY t1.id_consumer ASC LIMIT 20 OFFSET 0";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery7()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $query = new Query($table1);
        $query
            ->addOrderBy(OrderBy::asc($field10))
            ->addOrderBy(OrderBy::decs($field11));

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1 ORDER BY t1.id_consumer ASC, t1.id_brand_table_1 DESC LIMIT 20 OFFSET 0";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }


    /**
     * @throws Exception
     */
    public function testJoinQuery8()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $table2 = new Table("table_2", "t2");
        $field20 = Field::column($table2, "id", "consumer_id");

        $table3 = new Table("brand_table_1", "bt1");
        $field30 = Field::column($table3, "id", "brand_consumer_id");

        $table1
            ->addRelation(Relation::inner($field10, $field20))
            ->addRelation(Relation::inner($field11, $field30));

        $query = new Query($table1);
        $query->setFoundRows(false);

        $sql = new Sql();

        $sql->sentence = "SELECT t1.* FROM table_1 AS t1 LIMIT 20 OFFSET 0";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testJoinQuery9()
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
            ->setFoundRows(false)
            ->addField($field10)
            ->addField($field11)
            ->addField($field20)
            ->addField($field30)
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::gte($field23, 1))
                    ->add(Filter::isNull($field24))
            )
            ->addFilter(
                FilterGroup::or()
                    ->add(Filter::notIn($field13, ["ratia", "viloria"]))
                    ->add(Filter::ne($field14, false))
            )
            ->addGroupBy(GroupBy::create($field10))
            ->addGroupBy(GroupBy::create($field20))
            ->addGroupBy(GroupBy::create($field30))
            ->addOrderBy(OrderBy::asc($field10))
            ->addOrderBy(OrderBy::decs($field20))
            ->addOrderBy(OrderBy::asc($field30))
            ->setLimit(10)
            ->setOffset(3);

        $sql = new Sql();

        $sql->sentence = "SELECT t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, t2.id AS consumer_id, bt1.id AS brand_consumer_id FROM table_1 AS t1 INNER JOIN table_2 AS t2 ON t1.id_consumer = t2.id INNER JOIN brand_table_1 AS bt1 ON t1.id_brand_table_1 = bt1.id AND (bt1.allow_brand = ?) WHERE (t2.last_name >= ? OR t2.allow_newsletters IS NULL) AND (t1.last_name NOT IN (?,?) OR t1.allow_brand != ?) GROUP BY t1.id_consumer, t2.id, bt1.id ORDER BY t1.id_consumer ASC, t2.id DESC, bt1.id ASC LIMIT 10 OFFSET 3";
        $sql->params = [1, 1, 'ratia', 'viloria', 0];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    /**
     * @throws Exception
     */
    public function testSimpleQuery5()
    {
        $table1 = new Table("table_1", "t1");
        $field10 = Field::column($table1, "id_consumer", "register_id");
        $field11 = Field::column($table1, "id_brand_table_1", "id_brand_table_1");

        $query = new Query($table1);
        $query
            ->addField($field10)
            ->addField($field11)
            ->addField(Field::callback(
                function (array $rawRow) {
                    return $rawRow;
                }, 'tmp')
            );

        $sql = new Sql();

        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.id_consumer AS register_id, t1.id_brand_table_1 AS id_brand_table_1, 'CALLBACK' AS tmp FROM table_1 AS t1 LIMIT 20 OFFSET 0";
        $sql->params = [];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    public function testSimpleQuery6()
    {
        $table1 = new Table("multibrand_userdata", "mbud");
        $field10 = Field::column($table1, "created");
        $field11 = Field::custom($table1, "CURDATE()", "");
        $query = new Query($table1);
        $query
            ->addFilter(Filter::eq($field10, $field11));

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS mbud.* FROM multibrand_userdata AS mbud WHERE mbud.created = CURDATE() LIMIT 20 OFFSET 0";
        $sql->params = [];

        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }

    public function testLikeFilterQuery()
    {
        $table1 = new Table("table_1", "t1");
        $name = Field::column($table1, "name");
        $query = new Query($table1);
        $query->addFilter(
            FilterGroup::and()
                ->add(Filter::contain($name, "Federico"))
                ->add(Filter::endWith($name, "rico"))
                ->add(Filter::startWith($name, "Fede"))
                ->add(Filter::notContain($name, "Antonio"))
                ->add(Filter::notEndWith($name, "nio"))
                ->add(Filter::notStartWith($name, "Anto"))
        );


        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS t1.* FROM table_1 AS t1 WHERE (t1.name LIKE ? AND t1.name LIKE ? AND t1.name LIKE ? AND t1.name NOT LIKE ? AND t1.name NOT LIKE ? AND t1.name NOT LIKE ?) LIMIT 20 OFFSET 0";
        $sql->params = [
            '%Federico%',
            '%rico',
            'Fede%',
            '%Antonio%',
            '%nio',
            'Anto%'
        ];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());
    }


    public function testFilterQuery()
    {
        $table = new Table("multibrand_userdata");
        $id_field = Field::column($table, "id");
        $name_field = Field::column($table, "first_name");

        $filter_query = new Query($table);
        $filter_query
            ->addFilter(Filter::contain($name_field, "fede"))
            ->addField($id_field);

        $query = new Query($table);
        $query->addFilter(
            new FilterQuery(
                $id_field,
                IFilter::IN,
                $filter_query
            )
        );
        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS multibrand_userdata.* FROM multibrand_userdata WHERE multibrand_userdata.id IN (SELECT multibrand_userdata.id AS id FROM multibrand_userdata WHERE multibrand_userdata.first_name LIKE ?) LIMIT 20 OFFSET 0";
        $sql->params = [
            '%fede%'
        ];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());

    }

    public function testFilterQueryWithMoreFilters()
    {
        $table = new Table("multibrand_userdata");
        $id_field = Field::column($table, "id");
        $name_field = Field::column($table, "first_name");
        $last_field = Field::column($table, "last_name");

        $filter_query = new Query($table);
        $filter_query
            ->addFilter(Filter::contain($name_field, "fede"))
            ->addField($id_field);

        $query = new Query($table);
        $query
            ->addFilter(Filter::gt($id_field, 2))
            ->addFilter(
                new FilterQuery(
                    $id_field,
                    IFilter::IN,
                    $filter_query
                )
            )
            ->addFilter(Filter::notContain($last_field, "guerra"));
        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS multibrand_userdata.* FROM multibrand_userdata WHERE multibrand_userdata.id > ? AND multibrand_userdata.id IN (SELECT multibrand_userdata.id AS id FROM multibrand_userdata WHERE multibrand_userdata.first_name LIKE ?) AND multibrand_userdata.last_name NOT LIKE ? LIMIT 20 OFFSET 0";
        $sql->params = [
            2,
            '%fede%',
            '%guerra%'
        ];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());

    }
    public function testFilterQueryInFilterGroup()
    {
        $table = new Table("multibrand_userdata");
        $id_field = Field::column($table, "id");
        $name_field = Field::column($table, "first_name");
        $last_field = Field::column($table, "last_name");

        $filter_query = new Query($table);
        $filter_query
            ->addFilter(Filter::contain($name_field, "fede"))
            ->addField($id_field);

        $query = new Query($table);
        $query
            ->addFilter(
                FilterGroup::and()
                    ->add(Filter::gt($id_field, 2))
                    ->add(
                        new FilterQuery(
                            $id_field,
                            IFilter::IN,
                            $filter_query
                        )
                    )
            )
            ->addFilter(Filter::notContain($last_field, "guerra"));

        $sql = new Sql();
        $sql->sentence = "SELECT SQL_CALC_FOUND_ROWS multibrand_userdata.* FROM multibrand_userdata WHERE (multibrand_userdata.id > ? AND multibrand_userdata.id IN (SELECT multibrand_userdata.id AS id FROM multibrand_userdata WHERE multibrand_userdata.first_name LIKE ?)) AND multibrand_userdata.last_name NOT LIKE ? LIMIT 20 OFFSET 0";
        $sql->params = [
            2,
            '%fede%',
            '%guerra%'
        ];
        $this->assertEqualsCanonicalizing($sql, $query->toSQL());

    }

}