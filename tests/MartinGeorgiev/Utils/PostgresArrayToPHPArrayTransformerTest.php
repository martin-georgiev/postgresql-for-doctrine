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
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function throws_invalid_argument_exception_when_tries_to_transform_invalid_postgres_array(string $postgresValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue);
    }

    /**
     * @return array<string, array{postgresValue: string}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'multi-dimensioned array' => [
                'postgresValue' => '{{1,2,3},{4,5,6}}',
            ],
        ];
    }

    /**
     * @test
     */
    public function handles_empty_input(): void
    {
        $input = '';
        $result = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($input);
        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function handles_null_string_input(): void
    {
        $input = 'null';
        $result = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($input);
        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function preserves_numeric_precision(): void
    {
        $input = '{"9223372036854775808","1.23456789012345"}';
        $output = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($input);

        self::assertSame(['9223372036854775808', '1.23456789012345'], $output);
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
