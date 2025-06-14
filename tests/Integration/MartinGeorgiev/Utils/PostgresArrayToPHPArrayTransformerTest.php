<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Integration\MartinGeorgiev\TestCase;

class PostgresArrayToPHPArrayTransformerTest extends TestCase
{
    private const TABLE_NAME = 'array_test_table';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTable();
    }

    /**
     * @return array<int, array{0: array{description: string, input: array<int, string>}}>
     */
    public static function provideArrayTestCases(): array
    {
        return [
            [['description' => 'Simple array', 'input' => ['hello', 'world']]],
            [['description' => 'Empty array', 'input' => []]],
            [['description' => 'Single empty string', 'input' => ['']]],
            [['description' => 'Quotes and backslashes', 'input' => ['"quoted"', 'back\\slash']]],
            [['description' => 'Windows paths', 'input' => ['C:\\Windows\\System32']]],
            [['description' => 'Escaped quotes', 'input' => ['\"escaped\"']]],
            [['description' => 'Double backslashes', 'input' => ['\\\\double\\\\']]],
            [['description' => 'Unicode', 'input' => ['Hello 世界', '🌍 Earth']]],
            [['description' => 'Special chars', 'input' => ['!@#$%^&*()_+=-}{[]|":;\'?><,./']]],
            [['description' => 'GitHub #351', 'input' => ["!@#\\$%^&*()_+=-}{[]|\":;'\\?><,./"]]],
            [['description' => 'Curly braces', 'input' => ['{foo,bar}']]],
            [['description' => 'Spaces', 'input' => ['  spaces  ']]],
            [['description' => 'Trailing backslash', 'input' => ['trailing\\']]],
            [['description' => 'Leading backslash', 'input' => ['\\leading']]],
            [['description' => 'Mixed', 'input' => ['simple', '"quoted"', 'back\\slash', '']]],
        ];
    }

    /**
     * @param array{description: string, input: array<int, string>} $testCase
     */
    #[DataProvider('provideArrayTestCases')]
    public function test_array_round_trip(array $testCase): void
    {
        $id = $this->insertArray($testCase['input']);

        $this->assertArrayRoundTrip($id, $testCase['input'], $testCase['description']);
    }

    /**
     * @return array<int, array{0: array{description: string, input: string}}>
     */
    public static function provideInvalidArrayFormats(): array
    {
        return [
            [['description' => 'Multi-dimensional', 'input' => '{{1,2},{3,4}}']],
            [['description' => 'Unclosed quote', 'input' => '{1,2,"unclosed']],
            [['description' => 'Invalid format', 'input' => '{invalid"format}']],
            [['description' => 'Malformed nesting', 'input' => '{1,{2,3},4}']],
        ];
    }

    #[DataProvider('provideInvalidArrayFormats')]
    public function test_invalid_array_formats_throw_exceptions(array $testCase): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($testCase['input']); // @phpstan-ignore-line
    }

    private function createTestTable(): void
    {
        $this->dropTestTableIfItExists(self::TABLE_NAME);
        $this->connection->executeStatement(\sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                test_array TEXT[]
            )
        ', self::TABLE_NAME));
    }

    /**
     * @template T
     *
     * @param array<string, mixed> $params
     * @param callable(string): T  $transform
     *
     * @return T
     */
    private function retrieveFromDatabase(string $sql, array $params, callable $transform): mixed
    {
        $row = $this->connection->executeQuery($sql, $params)->fetchAssociative();

        if ($row === false || !isset($row['test_array']) || !\is_string($row['test_array'])) {
            throw new \RuntimeException('Failed to retrieve array data');
        }

        return $transform($row['test_array']);
    }

    /**
     * @return array<int, string>
     */
    private function retrieveArray(int $id): array
    {
        $row = $this->connection->executeQuery(
            \sprintf('SELECT test_array FROM %s WHERE id = :id', self::TABLE_NAME),
            ['id' => $id]
        )->fetchAssociative();

        if ($row === false || !isset($row['test_array'])) {
            throw new \RuntimeException(\sprintf('Failed to retrieve array data for ID %d', $id));
        }

        if (!\is_string($row['test_array'])) {
            throw new \RuntimeException(\sprintf('Expected string for test_array, got %s', \gettype($row['test_array'])));
        }

        /** @var string $postgresArray */
        $postgresArray = $row['test_array'];

        /** @var array<int, string> $result */
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    private function retrieveArrayAsText(int $id): string
    {
        /** @var string $result */
        $result = $this->retrieveFromDatabase(
            \sprintf('SELECT test_array::text FROM %s WHERE id = :id', self::TABLE_NAME),
            ['id' => $id],
            static fn (string $value): string => $value
        );

        return $result;
    }

    /**
     * @param array<int, string> $arrayData
     */
    private function insertArray(array $arrayData): int
    {
        $result = $this->connection->executeQuery(
            \sprintf('INSERT INTO %s (test_array) VALUES (:arrayData) RETURNING id', self::TABLE_NAME),
            ['arrayData' => $arrayData],
            ['arrayData' => 'text[]']
        );

        $row = $result->fetchAssociative();
        if ($row === false || !isset($row['id']) || !\is_numeric($row['id'])) {
            throw new \RuntimeException('Failed to insert array data');
        }

        return (int) $row['id'];
    }

    /**
     * @param array<int, string> $expected
     */
    private function assertArrayRoundTrip(int $id, array $expected, string $description): void
    {
        // Test direct retrieval
        $retrieved = $this->retrieveArray($id);
        self::assertEquals(
            $expected,
            $retrieved,
            \sprintf('Direct retrieval failed for %s', $description)
        );

        // Test text representation
        $postgresText = $this->retrieveArrayAsText($id);
        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresText);
        self::assertEquals(
            $expected,
            $parsed,
            \sprintf('Text representation parsing failed for %s', $description)
        );
    }
}
