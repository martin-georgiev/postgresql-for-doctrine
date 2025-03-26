<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\ArrayDataTransformer;
use PHPUnit\Framework\TestCase;

class ArrayDataTransformerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     *
     * @param array<int, array<string, array|string>> $phpValue
     */
    public function can_transform_from_php_value(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($postgresValue, ArrayDataTransformer::transformPHPArrayToPostgresTextArray($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     *
     * @param array<int, array<string, array|string>> $phpValue
     */
    public function can_transform_to_php_value(array $phpValue, string $postgresValue): void
    {
        self::assertEquals($phpValue, ArrayDataTransformer::transformPostgresTextArrayToPHPArray($postgresValue));
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
            'simple integer strings' => [
                'phpValue' => [
                    0 => 1,
                    1 => 2,
                    2 => 3,
                    3 => 4,
                ],
                'postgresValue' => '{1,2,3,4}',
            ],
            'decimal numbers represented as strings are preserved as strings' => [
                'phpValue' => [
                    0 => '1.23',
                    1 => '2.34',
                    2 => '3.45',
                    3 => '4.56',
                ],
                'postgresValue' => '{"1.23","2.34","3.45","4.56"}',
            ],
            'decimal numbers' => [
                'phpValue' => [
                    0 => 1.23,
                    1 => 2.34,
                    2 => 3.45,
                    3 => 4.56,
                ],
                'postgresValue' => '{1.23,2.34,3.45,4.56}',
            ],
            'mixed content with special characters' => [
                'phpValue' => [
                    0 => 'dfasdf',
                    1 => 'qw,,e{q"we',
                    2 => "'qrer'",
                    3 => 604,
                    4 => '"aaa","b""bb","ccc"',
                ],
                'postgresValue' => '{"dfasdf","qw,,e{q\"we","\'qrer\'",604,"\"aaa\",\"b\"\"bb\",\"ccc\""}',
            ],
            'empty strings' => [
                'phpValue' => [
                    0 => '',
                    1 => '',
                ],
                'postgresValue' => '{"",""}',
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'scientific notation as strings' => [
                'phpValue' => ['1.23e4', '2.34e5', '3.45e6'],
                'postgresValue' => '{"1.23e4","2.34e5","3.45e6"}',
            ],
            'scientific notation with negative exponents' => [
                'phpValue' => ['1.23e-4', '2.34e-5', '3.45e-6'],
                'postgresValue' => '{"1.23e-4","2.34e-5","3.45e-6"}',
            ],
            'whole floats that look like integers' => [
                'phpValue' => ['1.0', '2.00', '3.000', '4.0000'],
                'postgresValue' => '{"1.0","2.00","3.000","4.0000"}',
            ],
            'large integers beyond PHP_INT_MAX' => [
                'phpValue' => [
                    '9223372036854775808',  // PHP_INT_MAX + 1
                    '9999999999999999999',
                    '-9223372036854775809', // PHP_INT_MIN - 1
                ],
                'postgresValue' => '{"9223372036854775808","9999999999999999999","-9223372036854775809"}',
            ],
            'mixed numeric formats' => [
                'phpValue' => [
                    '1.23',                 // regular float string
                    1.23,                   // regular float
                    '1.230',                // float with trailing zeros
                    '1.23e4',               // scientific notation
                    '1.0',                  // whole float as string
                    1.0,                    // whole float
                    '9999999999999999999',  // large integer
                ],
                'postgresValue' => '{"1.23",1.23,"1.230","1.23e4","1.0",1,"9999999999999999999"}',
            ],
            'boolean values' => [
                'phpValue' => [true, false],
                'postgresValue' => '{true,false}',
            ],
            'objects with __toString' => [
                'phpValue' => [new class {
                    public function __toString(): string
                    {
                        return 'custom string';
                    }
                }],
                'postgresValue' => '{"custom string"}',
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
     *
     * @param array<int, mixed> $phpValue
     */
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_from_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ArrayDataTransformer::transformPHPArrayToPostgresTextArray($phpValue);
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     *
     * @param array<int, mixed> $phpValue
     */
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ArrayDataTransformer::transformPostgresTextArrayToPHPArray($postgresValue);
    }

    /**
     * @return list<array{
     *     phpValue: array,
     *     postgresValue: string
     * }>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            [
                'phpValue' => [
                    [
                        0 => '1-1',
                        1 => '1-2',
                        2 => '1-3',
                    ],
                    [
                        0 => '2-1',
                        1 => '2-2',
                        2 => '2-3',
                    ],
                ],
                'postgresValue' => '{{"1-1","1-2","1-3"},{"2-1","2-2","2-3"}}',
            ],
        ];
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_postgres_array_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid array format');
        ArrayDataTransformer::transformPostgresTextArrayToPHPArray('{invalid"format}');
    }

    /**
     * @test
     */
    public function preserves_numeric_string_types(): void
    {
        $input = ['1', '1.0', '1.00', 1, 1.01];
        $postgres = ArrayDataTransformer::transformPHPArrayToPostgresTextArray($input);
        $output = ArrayDataTransformer::transformPostgresTextArrayToPHPArray($postgres);

        self::assertSame([
            '1',
            '1.0',
            '1.00',
            1,
            1.01,
        ], $output);
    }

    /**
     * @test
     */
    public function handles_resource_cleanup(): void
    {
        $resource = \fopen('php://memory', 'r');
        \assert(\is_resource($resource));
        $input = [$resource];
        $result = ArrayDataTransformer::transformPHPArrayToPostgresTextArray($input);
        \fclose($resource);

        self::assertSame('{"(resource)"}', $result);
    }
}
