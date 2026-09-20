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
            'simple array' => [
                'phpValue' => ['hello', 'world'],
                'postgresLiteral' => '{hello,world}',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresLiteral' => '{}',
            ],
            'single empty string' => [
                'phpValue' => [''],
                'postgresLiteral' => '{""}',
            ],
            'quotes and backslashes' => [
                'phpValue' => ['"quoted"', 'back\\slash'],
                'postgresLiteral' => '{"\\"quoted\\"","back\\\\slash"}',
            ],
            'windows paths' => [
                'phpValue' => ['C:\\Windows\\System32'],
                'postgresLiteral' => '{"C:\\\\Windows\\\\System32"}',
            ],
            'escaped quotes' => [
                'phpValue' => ['\\"escaped\\"'],
                'postgresLiteral' => '{"\\\\\\"escaped\\\\\\""}',
            ],
            'double backslashes' => [
                'phpValue' => ['\\\\double\\\\'],
                'postgresLiteral' => '{"\\\\\\\\double\\\\\\\\"}',
            ],
            'special chars' => [
                'phpValue' => ['!@#$%^&*()_+=-}{[]|":;\'?><,./'],
                'postgresLiteral' => '{"!@#$%^&*()_+=-}{[]|\\":;\'?><,./"}',
            ],
            'spaces' => [
                'phpValue' => ['  spaces  '],
                'postgresLiteral' => '{"  spaces  "}',
            ],
            'trailing backslash' => [
                'phpValue' => ['trailing\\'],
                'postgresLiteral' => '{"trailing\\\\"}',
            ],
            'leading backslash' => [
                'phpValue' => ['\\leading'],
                'postgresLiteral' => '{"\\\\leading"}',
            ],
            'mixed' => [
                'phpValue' => ['simple', '"quoted"', 'back\\slash', ''],
                'postgresLiteral' => '{simple,"\\"quoted\\"","back\\\\slash",""}',
            ],
        ];
    }

    #[DataProvider('provideInvalidPostgresArrays')]
    #[Test]
    public function throws_exception_for_invalid_postgres_arrays(string $postgresArray): void
    {
        $this->expectException(InvalidArrayFormatException::class);

        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPostgresArrays(): array
    {
        return [
            'multi-dimensional' => ['{{1,2},{3,4}}'],
            'unclosed quote' => ['{1,2,"unclosed'],
            'quote inside an unquoted element' => ['{invalid"format}'],
            'nested array among the elements' => ['{1,{2,3},4}'],
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
}
