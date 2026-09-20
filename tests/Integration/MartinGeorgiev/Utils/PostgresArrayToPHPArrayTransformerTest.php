<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

final class PostgresArrayToPHPArrayTransformerTest extends TestCase
{
    private const TABLE_NAME = 'array_test_table';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTable();
    }

    /**
     * @param array{description: string, input: array<int, string>} $testCase
     */
    #[DataProvider('provideArrayTestCases')]
    #[Test]
    public function array_round_trip(array $testCase): void
    {
        $id = $this->insertArray($testCase['input']);

        $this->assertArrayRoundTrip($id, $testCase['input'], $testCase['description']);
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
            [['description' => 'GitHub #351 - regression #1', 'input' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./']]],
            [['description' => 'GitHub #351 - regression #2', 'input' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./', 'text']]],
            [['description' => 'Curly braces', 'input' => ['{foo,bar}']]],
            [['description' => 'Spaces', 'input' => ['  spaces  ']]],
            [['description' => 'Trailing backslash', 'input' => ['trailing\\']]],
            [['description' => 'Leading backslash', 'input' => ['\\leading']]],
            [['description' => 'Mixed', 'input' => ['simple', '"quoted"', 'back\\slash', '']]],
        ];
    }

    /**
     * @param array<array-key, string> $phpValue
     */
    #[DataProvider('provideExactRoundtripValues')]
    #[Test]
    public function postgres_stores_the_value_as_the_recorded_literal(array $phpValue, string $postgresLiteral): void
    {
        $id = $this->insertArray($phpValue);

        $this->assertSame($postgresLiteral, $this->retrieveArrayAsText($id));
    }

    /**
     * @param array<array-key, string> $phpValue
     */
    #[DataProvider('provideExactRoundtripValues')]
    #[Test]
    public function parses_the_recorded_literal_back_to_the_value(array $phpValue, string $postgresLiteral): void
    {
        $this->assertSame($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresLiteral, true));
    }

    /**
     * Each row is a value copied out of the unit test's provider together with the literal PostgreSQL wrote for it,
     * read back from the column as text. The unit test pairs the same values with literals written by hand, where
     * nothing checks them against a database.
     *
     * Only rows whose elements are all strings are here: a text[] column cannot remember that 1 arrived as an int
     * rather than the string '1', so type inference belongs to the parser and is tested there.
     *
     * @return array<string, array{phpValue: array<array-key, string>, postgresLiteral: string}>
     */
    public static function provideExactRoundtripValues(): array
    {
        return [
            'simple integer strings as strings are preserved as strings' => [
                'phpValue' => [
                    0 => '1',
                    1 => '2',
                    2 => '3',
                    3 => '4',
                ],
                'postgresLiteral' => '{1,2,3,4}',
            ],
            'simple strings' => [
                'phpValue' => [
                    0 => 'this',
                    1 => 'is',
                    2 => 'a',
                    3 => 'test',
                ],
                'postgresLiteral' => '{this,is,a,test}',
            ],
            'strings with special characters' => [
                'phpValue' => [
                    0 => 'this has "quotes"',
                    1 => 'this has \\\backslashes\\\\',
                ],
                'postgresLiteral' => '{"this has \\"quotes\\"","this has \\\\\\\\backslashes\\\\\\\\"}',
            ],
            'strings with backslashes' => [
                'phpValue' => ['path\to\file', 'C:\Windows\System32'],
                'postgresLiteral' => '{"path\\\\to\\\\file","C:\\\\Windows\\\\System32"}',
            ],
            'strings with unicode characters' => [
                'phpValue' => ['Hello 世界', '🌍 Earth'],
                'postgresLiteral' => '{"Hello 世界","🌍 Earth"}',
            ],
            'unquoted strings' => [
                'phpValue' => ['unquoted', 'strings'],
                'postgresLiteral' => '{unquoted,strings}',
            ],
            'mixed quoted and unquoted strings' => [
                'phpValue' => ['quoted', 'unquoted'],
                'postgresLiteral' => '{quoted,unquoted}',
            ],
            'with only backslashes' => [
                'phpValue' => ['\\'],
                'postgresLiteral' => '{"\\\\"}',
            ],
            'with only double quotes' => [
                'phpValue' => ['"'],
                'postgresLiteral' => '{"\\""}',
            ],
            'with empty quoted strings' => [
                'phpValue' => ['', ''],
                'postgresLiteral' => '{"",""}',
            ],
            'github #351 regression #1: string with special characters and backslash' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./'],
                'postgresLiteral' => '{"⥀!@#$%^&*()_+=-}{[]|\\":;\'\\\\?><,./"}',
            ],
            'github #351 regression #2: string with special characters, backslash and additional element' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./', 'text'],
                'postgresLiteral' => '{"⥀!@#$%^&*()_+=-}{[]|\\":;\'\\\\?><,./",text}',
            ],
            'backslash before backslash' => [
                'phpValue' => ['a\b'],
                'postgresLiteral' => '{"a\\\\b"}',
            ],
            'single backslash before non-escape char' => [
                'phpValue' => ['a\$b'],
                'postgresLiteral' => '{"a\\\\$b"}',
            ],
            'element with curly braces and comma' => [
                'phpValue' => ['{foo,bar}'],
                'postgresLiteral' => '{"{foo,bar}"}',
            ],
            'element with whitespace' => [
                'phpValue' => ['  foo  '],
                'postgresLiteral' => '{"  foo  "}',
            ],
            'element carrying the nested-array marker' => [
                'phpValue' => ['a},{b', '{x}'],
                'postgresLiteral' => '{"a},{b","{x}"}',
            ],
            'github #424 regression: numeric strings should be preserved as strings when unquoted' => [
                'phpValue' => ['1', 'test', 'true'],
                'postgresLiteral' => '{1,test,true}',
            ],
        ];
    }

    #[DataProvider('provideInvalidArrayFormats')]
    #[Test]
    public function invalid_array_formats_throw_exceptions(array $testCase): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($testCase['input']); // @phpstan-ignore-line
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
     * @param callable(string): T $transform
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

        $postgresArray = $row['test_array'];

        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray); // @phpstan-ignore-line
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
     * @param array<array-key, string> $arrayData
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
        $retrieved = $this->retrieveArray($id);
        $this->assertEquals(
            $expected,
            $retrieved,
            \sprintf('Direct retrieval failed for %s', $description)
        );

        $postgresText = $this->retrieveArrayAsText($id);
        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresText);
        $this->assertEquals(
            $expected,
            $parsed,
            \sprintf('Text representation parsing failed for %s', $description)
        );
    }
}
