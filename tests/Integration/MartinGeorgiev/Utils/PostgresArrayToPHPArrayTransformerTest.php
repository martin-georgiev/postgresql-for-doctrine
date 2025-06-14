<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use Tests\Integration\MartinGeorgiev\TestCase;

class PostgresArrayToPHPArrayTransformerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForArrayFixture();
    }

    public function test_round_trip_integrity_with_real_postgres(): void
    {
        $testCases = [
            // Simple cases
            ['hello', 'world'],
            ['123', '456'],
            ['true', 'false'],

            // Empty and null cases
            [],
            [''],
            ['', '', ''],

            // Special characters and escaping
            ['"quoted"', 'unquoted'],
            ['this has \\backslashes\\'],
            ['path\\to\\file', 'C:\\Windows\\System32'],

            // Complex escaping scenarios
            ['\"escaped\"'],
            ['\\\\double\\\\backslash\\\\'],

            // Unicode and special characters
            ['Hello 世界', '🌍 Earth'],
            ['!@#$%^&*()_+=-}{[]|":;\'?><,./'],

            // The GitHub #351 regression case
            ["!@#\\$%^&*()_+=-}{[]|\":;'\\?><,./"],

            // Edge cases
            ['{foo,bar}'],
            ['  spaces  '],
            ['trailing\\'],
            ['\\leading'],

            // Mixed cases
            ['simple', '"quoted"', 'back\\slash', ''],
        ];

        foreach ($testCases as $i => $originalArray) {
            $this->assertRoundTripIntegrity($originalArray, 'Test case '.$i);
        }
    }

    /**
     * Test storing and retrieving arrays through actual PostgreSQL text[] columns.
     */
    public function test_text_array_column_storage(): void
    {
        // Test data with various edge cases
        $testData = [
            ['simple', 'array'],
            ['with "quotes"', 'and \\backslashes\\'],
            [''],  // Empty array
            ['unicode 🎉', '世界'],
            ['!@#\\$%^&*()_+=-}{[]|":;\'\\?><,./'],  // GitHub #351 case
        ];

        foreach ($testData as $id => $arrayData) {
            // Store the array in PostgreSQL
            $this->insertArrayData($id, $arrayData);

            // Retrieve and verify
            $retrieved = $this->retrieveArrayData($id);

            self::assertEquals(
                $arrayData,
                $retrieved,
                \sprintf('Round-trip failed for test data ID %s: ', $id).\var_export($arrayData, true)
            );
        }
    }

    /**
     * Test PostgreSQL array representations that come from actual database queries.
     */
    public function test_real_postgres_array_representations(): void
    {
        // Insert various arrays and capture their actual PostgreSQL string representations
        $testArrays = [
            ['single'],
            ['one', 'two'],
            ['has "quotes"'],
            ['has \\backslashes\\'],
            [''],
            ['mixed', '"quoted"', 'back\\slash'],
        ];

        foreach ($testArrays as $i => $testArray) {
            // Store in database
            $this->insertArrayData($i, $testArray);

            // Get the raw PostgreSQL string representation
            $sql = 'SELECT test_array::text FROM array_test_table WHERE id = ?';
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(1, $i);
            $result = $stmt->executeQuery();
            $row = $result->fetchAssociative();

            $postgresRepresentation = $row['test_array'];

            // Test our transformer can handle the real representation
            $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresRepresentation);

            self::assertEquals(
                $testArray,
                $parsed,
                'Failed to parse real PostgreSQL representation: '.$postgresRepresentation
            );
        }
    }

    /**
     * Test error handling with invalid array formats.
     */
    public function test_invalid_array_formats_throw_exceptions(): void
    {
        $invalidFormats = [
            '{{1,2},{3,4}}',  // Multi-dimensional
            '{1,2,"unclosed',  // Unclosed quote
            '{invalid"format}',  // Invalid format
            '{1,{2,3},4}',  // Malformed nesting
        ];

        foreach ($invalidFormats as $invalidFormat) {
            $this->expectException(InvalidArrayFormatException::class);
            PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($invalidFormat);
        }
    }

    /**
     * Test arrays with NULL values through the database.
     */
    public function test_null_value_handling(): void
    {
        // Create test table that allows NULLs
        $sql = '
            CREATE TABLE null_test_table (
                id INTEGER PRIMARY KEY,
                nullable_array TEXT[]
            )
        ';
        $this->connection->executeStatement($sql);

        // Test array with NULL elements
        $this->connection->executeStatement(
            'INSERT INTO null_test_table (id, nullable_array) VALUES (1, \'{"item1", NULL, "item3"}\')'
        );

        $result = $this->connection->executeQuery('SELECT nullable_array::text FROM null_test_table WHERE id = 1');
        $row = $result->fetchAssociative();

        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($row['nullable_array']);
        self::assertEquals(['item1', null, 'item3'], $parsed);
    }

    /**
     * Test edge cases that were problematic in GitHub issue #351.
     */
    public function test_github_351_regression_scenarios(): void
    {
        $regressionCases = [
            // Original case from issue #351
            ["!@#\\$%^&*()_+=-}{[]|\":;'\\?><,./"],

            // Related edge cases with backslashes and quotes
            ['\\before'],
            ['after\\'],
            ['\\middle\\'],
            ['\\\\double\\\\'],
            ['\"quoted\"'],
            ['\\\"escaped\\\"'],

            // Special characters that could cause parsing issues
            ['{curly}', '[square]', '(parens)'],
            ['comma,separated', 'semicolon;separated'],
            ['pipe|separated', 'colon:separated'],
        ];

        foreach ($regressionCases as $i => $testCase) {
            $this->assertRoundTripIntegrity($testCase, 'GitHub #351 regression case '.$i);
        }
    }

    /**
     * Helper method to test round-trip integrity.
     */
    private function assertRoundTripIntegrity(array $originalArray, string $testDescription): void
    {
        // Store in PostgreSQL using ARRAY constructor
        $placeholders = \str_repeat('?,', \count($originalArray));
        $placeholders = \rtrim($placeholders, ',');

        if ($originalArray === []) {
            $sql = "SELECT '{}'::text[]::text";
            $stmt = $this->connection->prepare($sql);
        } else {
            $sql = \sprintf('SELECT ARRAY[%s]::text[]::text', $placeholders);
            $stmt = $this->connection->prepare($sql);
            foreach ($originalArray as $i => $value) {
                $stmt->bindValue($i + 1, $value);
            }
        }

        $result = $stmt->executeQuery();
        $row = $result->fetchAssociative();
        $postgresString = $row['array'];

        // Parse back using our transformer
        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresString);

        self::assertEquals(
            $originalArray,
            $parsed,
            \sprintf('Round-trip integrity failed for %s. PostgreSQL representation: %s', $testDescription, $postgresString)
        );
    }

    private function createTestTableForArrayFixture(): void
    {
        $this->dropTestTableIfItExists('array_test_table');

        $sql = '
            CREATE TABLE array_test_table (
                id INTEGER PRIMARY KEY,
                test_array TEXT[]
            )
        ';
        $this->connection->executeStatement($sql);
    }

    private function insertArrayData(int $id, array $arrayData): void
    {
        $sql = 'INSERT INTO array_test_table (id, test_array) VALUES (:id, :arrayData)';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id);
        $statement->bindValue('arrayData', $arrayData, 'text[]');
        $statement->executeStatement();
    }

    private function retrieveArrayData(int $id): array
    {
        $sql = 'SELECT test_array FROM array_test_table WHERE id = :id';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id);

        $result = $statement->executeQuery();
        $row = $result->fetchAssociative();

        return $this->transformPostgresArray($row['test_array']);
    }
}
