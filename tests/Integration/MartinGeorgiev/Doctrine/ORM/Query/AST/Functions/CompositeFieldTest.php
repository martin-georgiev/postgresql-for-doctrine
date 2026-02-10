<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CompositeField;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

class CompositeFieldTest extends BaseTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COMPOSITE_FIELD' => CompositeField::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCompositeTypeAndTable();
        $this->insertTestData();
    }

    protected function createCompositeTypeAndTable(): void
    {
        $this->createTestSchema();

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.inventory_item AS (
                name TEXT,
                supplier_id INTEGER,
                price NUMERIC(10,2)
            )',
            self::DATABASE_SCHEMA
        ));

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.address AS (
                street TEXT,
                city TEXT,
                zip TEXT
            )',
            self::DATABASE_SCHEMA
        ));

        $this->connection->executeStatement(\sprintf(
            'CREATE TYPE %s.special_fields AS (
                "camelCaseField" TEXT,
                "PascalCaseField" TEXT,
                "order" INTEGER,
                "select" TEXT,
                "user" TEXT
            )',
            self::DATABASE_SCHEMA
        ));

        $this->dropTestTableIfItExists('containscomposites');

        $this->connection->executeStatement(\sprintf(
            'CREATE TABLE %s.containscomposites (
                id SERIAL PRIMARY KEY,
                item %s.inventory_item,
                address %s.address,
                special %s.special_fields
            )',
            self::DATABASE_SCHEMA,
            self::DATABASE_SCHEMA,
            self::DATABASE_SCHEMA,
            self::DATABASE_SCHEMA
        ));
    }

    protected function insertTestData(): void
    {
        $this->connection->executeStatement(\sprintf(
            "INSERT INTO %s.containscomposites (item, address, special) VALUES
            (ROW('Widget', 1, 9.99), ROW('123 Main St', 'New York', '10001'), ROW('camelValue', 'PascalValue', 1, 'selectValue', 'userValue')),
            (ROW('Gadget', 2, 19.99), ROW('456 Oak Ave', 'Boston', '02101'), ROW('anotherCamel', 'AnotherPascal', 2, 'anotherSelect', 'anotherUser')),
            (ROW('Gizmo', 3, 29.99), ROW('789 Pine Rd', 'Chicago', '60601'), ROW('thirdCamel', 'ThirdPascal', 3, 'thirdSelect', 'thirdUser'))",
            self::DATABASE_SCHEMA
        ));
    }

    #[Test]
    public function can_access_text_field_from_composite_type(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.item, 'name') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Widget', $result[0]['result']);
    }

    #[Test]
    public function can_access_integer_field_from_composite_type(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.item, 'supplier_id') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(2, (int) $result[0]['result']);
    }

    #[Test]
    public function can_access_numeric_field_from_composite_type(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.item, 'price') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertEqualsWithDelta(29.99, (float) $result[0]['result'], 0.001);
    }

    #[Test]
    public function can_use_composite_field_in_where_clause(): void
    {
        $dql = "SELECT t.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE COMPOSITE_FIELD(t.item, 'price') > 15";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function can_access_multiple_fields_from_same_composite(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.address, 'street') as street, COMPOSITE_FIELD(t.address, 'zip') as zip FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('456 Oak Ave', $result[0]['street']);
        $this->assertSame('02101', $result[0]['zip']);
    }

    #[Test]
    public function can_access_camel_case_field_name(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.special, 'camelCaseField') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('camelValue', $result[0]['result']);
    }

    #[Test]
    public function can_access_pascal_case_field_name(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.special, 'PascalCaseField') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('PascalValue', $result[0]['result']);
    }

    #[Test]
    public function can_access_reserved_word_order_field(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.special, 'order') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsNumeric($result[0]['result']);
        $this->assertSame(2, (int) $result[0]['result']);
    }

    #[Test]
    public function can_access_reserved_word_select_field(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.special, 'select') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('selectValue', $result[0]['result']);
    }

    #[Test]
    public function can_access_reserved_word_user_field(): void
    {
        $dql = "SELECT COMPOSITE_FIELD(t.special, 'user') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsComposites t WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('thirdUser', $result[0]['result']);
    }
}
