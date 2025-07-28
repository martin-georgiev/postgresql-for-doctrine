<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

class RangeOperatorsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForRangeFixture();
        $this->insertTestDataForRangeFixture();
    }

    protected function createTestTableForRangeFixture(): void
    {
        $tableName = 'rangeoperators';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                int4range_field INT4RANGE,
                int8range_field INT8RANGE,
                numrange_field NUMRANGE,
                daterange_field DATERANGE,
                tsrange_field TSRANGE,
                tstzrange_field TSTZRANGE,
                integer_array_field INTEGER[],
                numeric_array_field NUMERIC[],
                date_array_field DATE[]
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForRangeFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.rangeoperators (
                int4range_field, 
                int8range_field, 
                numrange_field, 
                daterange_field, 
                tsrange_field, 
                tstzrange_field,
                integer_array_field,
                numeric_array_field,
                date_array_field
            ) VALUES 
            (\'[1,10)\', \'[100,1000)\', \'[1.5,10.7)\', \'[2023-01-01,2023-12-31)\', \'[2023-01-01 10:00:00,2023-01-01 18:00:00)\', \'[2023-01-01 10:00:00+00,2023-01-01 18:00:00+00)\', ARRAY[1,2,3,4,5], ARRAY[1.5,2.5,3.5], ARRAY[\'2023-01-01\'::date, \'2023-01-02\'::date, \'2023-01-03\'::date]),
            (\'[5,15)\', \'[500,1500)\', \'[5.5,15.7)\', \'[2023-06-01,2023-12-31)\', \'[2023-06-01 10:00:00,2023-06-01 18:00:00)\', \'[2023-06-01 10:00:00+00,2023-06-01 18:00:00+00)\', ARRAY[5,6,7,8,9], ARRAY[5.5,6.5,7.5], ARRAY[\'2023-06-01\'::date, \'2023-06-02\'::date, \'2023-06-03\'::date]),
            (\'[20,30)\', \'[2000,3000)\', \'[20.5,30.7)\', \'[2023-12-01,2023-12-31)\', \'[2023-12-01 10:00:00,2023-12-01 18:00:00)\', \'[2023-12-01 10:00:00+00,2023-12-01 18:00:00+00)\', ARRAY[20,21,22,23,24], ARRAY[20.5,21.5,22.5], ARRAY[\'2023-12-01\'::date, \'2023-12-02\'::date, \'2023-12-03\'::date])
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }

    public function test_range_contains_operator_int4range(): void
    {
        // Test if one range contains another
        $sql = "SELECT id FROM test.rangeoperators WHERE int4range_field @> '[3,7)'::int4range";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only record with range [1,10) (contains [3,7))
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_contains_operator_numrange(): void
    {
        // Test if one range contains another
        $sql = "SELECT id FROM test.rangeoperators WHERE numrange_field @> '[2.5,8.5)'::numrange";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only record with range [1.5,10.7) (contains [2.5,8.5))
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_contains_operator_daterange(): void
    {
        // Test if one range contains another
        $sql = "SELECT id FROM test.rangeoperators WHERE daterange_field @> '[2023-06-15,2023-06-30)'::daterange";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records with ranges [2023-01-01,2023-12-31) and [2023-06-01,2023-12-31)
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_range_overlaps_operator_int4range(): void
    {
        // Test if ranges overlap
        $sql = "SELECT id FROM test.rangeoperators WHERE int4range_field && '[8,12)'::int4range";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records with ranges [1,10) and [5,15)
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_range_overlaps_operator_tsrange(): void
    {
        // Test if timestamp ranges overlap
        $sql = "SELECT id FROM test.rangeoperators WHERE tsrange_field && '[2023-01-01 15:00:00,2023-01-01 20:00:00)'::tsrange";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_is_contained_by_operator_int4range(): void
    {
        // Test if range is contained by another
        $sql = "SELECT id FROM test.rangeoperators WHERE int4range_field <@ '[0,20)'::int4range";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records with ranges [1,10) and [5,15)
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_array_contains_range_value(): void
    {
        // Test if array contains a value using ANY() function
        $sql = 'SELECT id FROM test.rangeoperators WHERE 3 = ANY(integer_array_field)';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result); // PostgreSQL returns integers as integers
    }

    public function test_array_contains_range_value_numeric(): void
    {
        // Test if numeric array contains a value using ANY() function
        $sql = 'SELECT id FROM test.rangeoperators WHERE 2.5 = ANY(numeric_array_field)';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_array_contains_range_value_date(): void
    {
        // Test if date array contains a value using ANY() function
        $sql = "SELECT id FROM test.rangeoperators WHERE '2023-01-02'::date = ANY(date_array_field)";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_value_is_contained_by_array(): void
    {
        // Test if a value is contained by an array using ANY() function
        $sql = 'SELECT id FROM test.rangeoperators WHERE 3 = ANY(integer_array_field)';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_equals_operator(): void
    {
        // Test if ranges are equal
        $sql = "SELECT id FROM test.rangeoperators WHERE int4range_field = '[1,10)'::int4range";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_range_not_equals_operator(): void
    {
        // Test if ranges are not equal
        $sql = "SELECT id FROM test.rangeoperators WHERE int4range_field != '[1,10)'::int4range";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records 2 and 3
        $this->assertContains(['id' => 2], $result);
        $this->assertContains(['id' => 3], $result);
    }

    public function test_range_contains_element(): void
    {
        // Test if range contains a specific element
        $sql = 'SELECT id FROM test.rangeoperators WHERE int4range_field @> 5';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records with ranges [1,10) and [5,15)
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_element_is_contained_by_range(): void
    {
        // Test if an element is contained by a range
        $sql = 'SELECT id FROM test.rangeoperators WHERE 5 <@ int4range_field';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(2, $result); // Should match records with ranges [1,10) and [5,15)
        $this->assertContains(['id' => 1], $result);
        $this->assertContains(['id' => 2], $result);
    }

    public function test_range_operations_with_tstzrange(): void
    {
        // Test timestamp with timezone range operations
        $sql = "SELECT id FROM test.rangeoperators WHERE tstzrange_field @> '2023-01-01 14:00:00+00'::timestamptz";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record
        $this->assertContains(['id' => 1], $result);
    }

    public function test_empty_range_operations(): void
    {
        // Test operations with empty ranges
        $sql = "SELECT id FROM test.rangeoperators WHERE 'empty'::int4range @> int4range_field";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(0, $result); // Empty range should not contain any non-empty ranges
    }

    public function test_infinite_range_operations(): void
    {
        // Test operations with infinite ranges
        $sql = "SELECT id FROM test.rangeoperators WHERE '(,)'::int4range @> int4range_field";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(3, $result); // Infinite range should contain all finite ranges
    }

    public function test_range_boundary_conditions(): void
    {
        // Test boundary conditions with inclusive/exclusive bounds
        $sql = 'SELECT id FROM test.rangeoperators WHERE int4range_field @> 1 AND int4range_field @> 9';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only the first record [1,10) which contains both 1 and 9
        $this->assertContains(['id' => 1], $result);
    }

    public function test_complex_range_queries(): void
    {
        // Test complex queries combining multiple range operations
        $sql = 'SELECT id FROM test.rangeoperators
                WHERE int4range_field @> \'[3,7)\'::int4range
                AND numrange_field @> \'[2.5,8.5)\'::numrange
                AND daterange_field @> \'[2023-06-15,2023-06-30)\'::daterange';
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $this->assertCount(1, $result); // Should match only record 1 (all conditions met)
        $this->assertContains(['id' => 1], $result);
    }
}
