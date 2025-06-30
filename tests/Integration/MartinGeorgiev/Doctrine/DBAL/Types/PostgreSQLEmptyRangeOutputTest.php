<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

/**
 * Tests to verify that PostgreSQL outputs 'empty' for empty ranges,
 * confirming our EMPTY_RANGE_STRING constant matches PostgreSQL behavior.
 */
final class PostgreSQLEmptyRangeOutputTest extends TestCase
{
    #[Test]
    public function postgres_outputs_empty_for_numrange_with_same_bounds_exclusive_upper(): void
    {
        $sql = "SELECT '[4,4)'::numrange AS range_output";
        $result = $this->connection->fetchAssociative($sql);
        $actualOutput = $result['range_output'];

        self::assertSame(
            'empty',
            $actualOutput,
            \sprintf("PostgreSQL outputs '%s' for empty ranges, but our constant expects 'empty'", $actualOutput)
        );
    }

    #[Test]
    public function postgres_outputs_empty_for_int4range_with_same_bounds_exclusive_upper(): void
    {
        $sql = "SELECT '[5,5)'::int4range AS range_output";
        $result = $this->connection->fetchAssociative($sql);
        $actualOutput = $result['range_output'];

        self::assertSame(
            'empty',
            $actualOutput,
            \sprintf("PostgreSQL outputs '%s' for empty INT4RANGE, but our constant expects 'empty'", $actualOutput)
        );
    }

    #[Test]
    public function postgres_outputs_empty_for_daterange_with_same_date_exclusive_upper(): void
    {
        $sql = "SELECT '[2023-01-01,2023-01-01)'::daterange AS range_output";
        $result = $this->connection->fetchAssociative($sql);
        $actualOutput = $result['range_output'];

        self::assertSame(
            'empty',
            $actualOutput,
            \sprintf("PostgreSQL outputs '%s' for empty DATERANGE, but our constant expects 'empty'", $actualOutput)
        );
    }

    #[Test]
    public function postgres_outputs_empty_for_explicit_empty_input(): void
    {
        $sql = "SELECT 'empty'::numrange AS range_output";
        $result = $this->connection->fetchAssociative($sql);
        $actualOutput = $result['range_output'];

        self::assertSame(
            'empty',
            $actualOutput,
            \sprintf("PostgreSQL outputs '%s' for explicit 'empty' input, but our constant expects 'empty'", $actualOutput)
        );
    }

    #[Test]
    public function postgres_isempty_function_correctly_identifies_empty_ranges(): void
    {
        $sql = "
            SELECT 
                '[4,4)'::numrange AS range1_text,
                isempty('[4,4)'::numrange) AS range1_is_empty,
                '[5,5)'::int4range AS range2_text,
                isempty('[5,5)'::int4range) AS range2_is_empty,
                'empty'::numrange AS range3_text,
                isempty('empty'::numrange) AS range3_is_empty
        ";
        $row = $this->connection->fetchAssociative($sql);

        self::assertSame('empty', $row['range1_text']);
        self::assertTrue($row['range1_is_empty']);

        self::assertSame('empty', $row['range2_text']);
        self::assertTrue($row['range2_is_empty']);

        self::assertSame('empty', $row['range3_text']);
        self::assertTrue($row['range3_is_empty']);
    }

    #[Test]
    public function postgres_handles_lower_greater_than_upper_as_empty(): void
    {
        $sql = "SELECT '[10,5)'::numrange AS range_output, isempty('[10,5)'::numrange) AS is_empty";
        $result = $this->connection->fetchAssociative($sql);

        self::assertSame('empty', $result['range_output']);
        self::assertTrue($result['is_empty']);
    }

    #[Test]
    public function postgres_handles_equal_bounds_with_exclusive_brackets_as_empty(): void
    {
        $sql = "SELECT '(5,5)'::numrange AS range_output, isempty('(5,5)'::numrange) AS is_empty";
        $result = $this->connection->fetchAssociative($sql);

        self::assertSame('empty', $result['range_output']);
        self::assertTrue($result['is_empty']);
    }

    #[Test]
    public function postgres_handles_equal_bounds_with_mixed_brackets_as_empty(): void
    {
        $sql = "SELECT '(5,5]'::numrange AS range_output, isempty('(5,5]'::numrange) AS is_empty";
        $result = $this->connection->fetchAssociative($sql);

        self::assertSame('empty', $result['range_output']);
        self::assertTrue($result['is_empty']);
    }

    #[Test]
    public function postgres_does_not_treat_equal_bounds_with_inclusive_brackets_as_empty(): void
    {
        $sql = "SELECT '[5,5]'::numrange AS range_output, isempty('[5,5]'::numrange) AS is_empty";
        $result = $this->connection->fetchAssociative($sql);

        self::assertSame('[5,5]', $result['range_output']);
        self::assertFalse($result['is_empty']);
    }
}
