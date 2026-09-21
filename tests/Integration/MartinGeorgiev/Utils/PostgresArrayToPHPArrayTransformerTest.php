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
    /**
     * @var string
     */
    private const TABLE_NAME = 'array_test_table';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTable();
    }

    /**
     * @param array<array-key, string> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(array $phpValue, string $postgresValue): void
    {
        $id = $this->insertArray($phpValue);

        $this->assertSame($postgresValue, $this->retrieveArrayAsText($id));
        $this->assertSame($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue, true));
    }

    /**
     * @return array<string, array{phpValue: array<array-key, string>, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple integer strings as strings are preserved as strings' => [
                'phpValue' => [
                    0 => '1',
                    1 => '2',
                    2 => '3',
                    3 => '4',
                ],
                'postgresValue' => '{1,2,3,4}',
            ],
            'simple strings' => [
                'phpValue' => [
                    0 => 'this',
                    1 => 'is',
                    2 => 'a',
                    3 => 'test',
                ],
                'postgresValue' => '{this,is,a,test}',
            ],
            'strings with special characters' => [
                'phpValue' => [
                    0 => 'this has "quotes"',
                    1 => 'this has \\\backslashes\\\\',
                ],
                'postgresValue' => '{"this has \\"quotes\\"","this has \\\\\\\\backslashes\\\\\\\\"}',
            ],
            'strings with backslashes' => [
                'phpValue' => ['path\to\file', 'C:\Windows\System32'],
                'postgresValue' => '{"path\\\\to\\\\file","C:\\\\Windows\\\\System32"}',
            ],
            'strings with unicode characters' => [
                'phpValue' => ['Hello 世界', '🌍 Earth'],
                'postgresValue' => '{"Hello 世界","🌍 Earth"}',
            ],
            'unquoted strings' => [
                'phpValue' => ['unquoted', 'strings'],
                'postgresValue' => '{unquoted,strings}',
            ],
            'mixed quoted and unquoted strings' => [
                'phpValue' => ['quoted', 'unquoted'],
                'postgresValue' => '{quoted,unquoted}',
            ],
            'with only backslashes' => [
                'phpValue' => ['\\'],
                'postgresValue' => '{"\\\\"}',
            ],
            'with only double quotes' => [
                'phpValue' => ['"'],
                'postgresValue' => '{"\\""}',
            ],
            'with empty quoted strings' => [
                'phpValue' => ['', ''],
                'postgresValue' => '{"",""}',
            ],
            'github #351 regression #1: string with special characters and backslash' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./'],
                'postgresValue' => '{"⥀!@#$%^&*()_+=-}{[]|\\":;\'\\\\?><,./"}',
            ],
            'github #351 regression #2: string with special characters, backslash and additional element' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./', 'text'],
                'postgresValue' => '{"⥀!@#$%^&*()_+=-}{[]|\\":;\'\\\\?><,./",text}',
            ],
            'backslash before backslash' => [
                'phpValue' => ['a\b'],
                'postgresValue' => '{"a\\\\b"}',
            ],
            'single backslash before non-escape char' => [
                'phpValue' => ['a\$b'],
                'postgresValue' => '{"a\\\\$b"}',
            ],
            'element with curly braces and comma' => [
                'phpValue' => ['{foo,bar}'],
                'postgresValue' => '{"{foo,bar}"}',
            ],
            'element with whitespace' => [
                'phpValue' => ['  foo  '],
                'postgresValue' => '{"  foo  "}',
            ],
            'element carrying the nested-array marker' => [
                'phpValue' => ['a},{b', '{x}'],
                'postgresValue' => '{"a},{b","{x}"}',
            ],
            'github #424 regression: numeric strings should be preserved as strings when unquoted' => [
                'phpValue' => ['1', 'test', 'true'],
                'postgresValue' => '{1,test,true}',
            ],
            'simple array' => [
                'phpValue' => ['hello', 'world'],
                'postgresValue' => '{hello,world}',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single empty string' => [
                'phpValue' => [''],
                'postgresValue' => '{""}',
            ],
            'quotes and backslashes' => [
                'phpValue' => ['"quoted"', 'back\\slash'],
                'postgresValue' => '{"\\"quoted\\"","back\\\\slash"}',
            ],
            'windows paths' => [
                'phpValue' => ['C:\\Windows\\System32'],
                'postgresValue' => '{"C:\\\\Windows\\\\System32"}',
            ],
            'escaped quotes' => [
                'phpValue' => ['\\"escaped\\"'],
                'postgresValue' => '{"\\\\\\"escaped\\\\\\""}',
            ],
            'double backslashes' => [
                'phpValue' => ['\\\\double\\\\'],
                'postgresValue' => '{"\\\\\\\\double\\\\\\\\"}',
            ],
            'special chars' => [
                'phpValue' => ['!@#$%^&*()_+=-}{[]|":;\'?><,./'],
                'postgresValue' => '{"!@#$%^&*()_+=-}{[]|\\":;\'?><,./"}',
            ],
            'spaces' => [
                'phpValue' => ['  spaces  '],
                'postgresValue' => '{"  spaces  "}',
            ],
            'trailing backslash' => [
                'phpValue' => ['trailing\\'],
                'postgresValue' => '{"trailing\\\\"}',
            ],
            'leading backslash' => [
                'phpValue' => ['\\leading'],
                'postgresValue' => '{"\\\\leading"}',
            ],
            'mixed' => [
                'phpValue' => ['simple', '"quoted"', 'back\\slash', ''],
                'postgresValue' => '{simple,"\\"quoted\\"","back\\\\slash",""}',
            ],
        ];
    }

    #[DataProvider('provideAcceptedLiterals')]
    #[Test]
    public function parses_literal_like_postgres(string $postgresValue): void
    {
        // Dollar quoting keeps the statement lexer out of it.
        // What comes back is array_in's own reading rather than anything standard_conforming_strings decides.
        $sql = \sprintf('SELECT (%s::text[])[1] AS element', '$$'.$postgresValue.'$$');

        $this->assertSame(
            $this->connection->executeQuery($sql)->fetchOne(),
            PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue, true)[0]
        );
    }

    /**
     * Literals array_in reads, and array_out never writes, which is why they carry no value to pair with.
     * The expectation for them comes from the database itself.
     *
     * @return array<string, array{string}>
     */
    public static function provideAcceptedLiterals(): array
    {
        return [
            'backslash before an ordinary character' => ['{"a\xb"}'],
            'backslash before a dollar sign' => ['{"a\$b"}'],
            'backslash before a question mark' => ['{"a\?b"}'],
            'backslash before a digit' => ['{"a\1b"}'],
            'escaped backslash' => ['{"a\\\\b"}'],
            'escaped quote' => ['{"a\"b"}'],
            'unquoted element' => ['{abc}'],
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
