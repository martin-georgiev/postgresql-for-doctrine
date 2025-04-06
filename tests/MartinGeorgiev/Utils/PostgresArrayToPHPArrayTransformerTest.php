<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\TestCase;

class PostgresArrayToPHPArrayTransformerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     *
     * @param array<int, array<string, array|string>> $phpValue
     */
    public function can_transform_to_php_value(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue));
    }

    /**
     * @return array<string, array{phpValue: array, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null value' => [
                'phpValue' => [],
                'postgresValue' => 'null',
            ],
            'empty value' => [
                'phpValue' => [],
                'postgresValue' => '',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'simple integer strings as strings are preserved as strings' => [
                'phpValue' => [
                    0 => '1',
                    1 => '2',
                    2 => '3',
                    3 => '4',
                ],
                'postgresValue' => '{"1","2","3","4"}',
            ],
            'simple integer strings as integers are preserved as integers' => [
                'phpValue' => [
                    0 => 1,
                    1 => 2,
                    2 => 3,
                    3 => 4,
                ],
                'postgresValue' => '{1,2,3,4}',
            ],
            'float values' => [
                'phpValue' => [
                    0 => 1.5,
                    1 => 2.75,
                    2 => -3.25,
                ],
                'postgresValue' => '{1.5,2.75,-3.25}',
            ],
            'scientific notation' => [
                'phpValue' => [
                    0 => 1.5e3,
                    1 => 2.75e-2,
                ],
                'postgresValue' => '{1.5e3,2.75e-2}',
            ],
            'boolean values' => [
                'phpValue' => [
                    0 => true,
                    1 => false,
                    2 => true,
                    3 => false,
                ],
                'postgresValue' => '{true,false,t,f}',
            ],
            'null values' => [
                'phpValue' => [
                    0 => null,
                    1 => 'not null',
                    2 => null,
                ],
                'postgresValue' => '{NULL,"not null",null}',
            ],
            'simple strings' => [
                'phpValue' => [
                    0 => 'this',
                    1 => 'is',
                    2 => 'a',
                    3 => 'test',
                ],
                'postgresValue' => '{"this","is","a","test"}',
            ],
            'strings with special characters' => [
                'phpValue' => [
                    0 => 'this has "quotes"',
                    1 => 'this has \\\backslashes\\\\',
                ],
                'postgresValue' => '{"this has \"quotes\"","this has \\\\\\\backslashes\\\\\\\"}',
            ],
            'strings with backslashes' => [
                'phpValue' => ['path\to\file', 'C:\Windows\System32'],
                'postgresValue' => '{"path\\\to\\\file","C:\\\Windows\\\System32"}',
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
                'postgresValue' => '{"quoted",unquoted}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideMultiDimensionalArrays
     */
    public function throws_exception_for_multi_dimensional_arrays(string $postgresValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        $this->expectExceptionMessage('Only single-dimensioned arrays are supported');
        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue);
    }

    /**
     * @return array<string, array{postgresValue: string}>
     */
    public static function provideMultiDimensionalArrays(): array
    {
        return [
            'multi-dimensioned array' => [
                'postgresValue' => '{{1,2,3},{4,5,6}}',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideManualParsingArrays
     */
    public function can_recover_from_json_decode_failure_and_transform_value_through_manual_parsing(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue));
    }

    /**
     * @return array<string, array{phpValue: array, postgresValue: string}>
     */
    public static function provideManualParsingArrays(): array
    {
        return [
            'manual parsing of unquoted only text' => [
                'phpValue' => ['unquoted string', 'another unquoted string'],
                'postgresValue' => '{unquoted string,another unquoted string}',
            ],
            'manual parsing with escaping' => [
                'phpValue' => ['escaped " quote', 'unescaped'],
                'postgresValue' => '{"escaped \" quote",unescaped}',
            ],
            'manual parsing with trailing backslash' => [
                'phpValue' => ['backslash\\', 'another\one'],
                'postgresValue' => '{backslash\,another\one}',
            ],
        ];
    }

    /**
     * @test
     */
    public function can_transform_escaped_quotes_with_backslashes(): void
    {
        $postgresArray = '{"\\\"quoted\\\""}';
        self::assertSame(['\\\"quoted\\\"'], PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray));
    }

    /**
     * @test
     */
    public function can_preserves_numeric_precision(): void
    {
        $postgresArray = '{"9223372036854775808","1.23456789012345"}';
        self::assertSame(['9223372036854775808', '1.23456789012345'], PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray));
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidPostgresArrays
     */
    public function throws_exception_for_invalid_postgres_arrays(string $postgresValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        $this->expectExceptionMessage('Invalid array format');
        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue);
    }

    /**
     * @return array<string, array{postgresValue: string}>
     */
    public static function provideInvalidPostgresArrays(): array
    {
        return [
            'unclosed string' => [
                'postgresValue' => '{1,2,"unclosed string}',
            ],
            'invalid format' => [
                'postgresValue' => '{invalid"format}',
            ],
            'malformed nesting' => [
                'postgresValue' => '{1,{2,3},4}',
            ],
        ];
    }
}
