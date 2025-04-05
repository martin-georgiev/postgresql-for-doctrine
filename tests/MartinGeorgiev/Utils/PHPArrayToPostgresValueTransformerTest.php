<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PHPArrayToPostgresValueTransformer;
use PHPUnit\Framework\TestCase;

class PHPArrayToPostgresValueTransformerTest extends TestCase
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
        self::assertEquals($postgresValue, PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpValue));
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
                    1 => 'this has \backslashes\\',
                ],
                'postgresValue' => '{"this has \"quotes\"","this has \\\backslashes\\\"}',
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
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_from_php_value(array $phpValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpValue);
    }

    /**
     * @return array<string, array{phpValue: array, postgresValue: string}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'multi-dimensioned array' => [
                'phpValue' => [
                    0 => [
                        'this',
                        'is',
                        'a',
                        'test',
                    ],
                ],
                'postgresValue' => '',
            ],
        ];
    }

    /**
     * @test
     */
    public function handles_resource_cleanup(): void
    {
        $resource = \fopen('php://memory', 'r');
        \assert(\is_resource($resource));
        $input = [$resource];
        $result = PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($input);
        \fclose($resource);

        self::assertSame('{"(resource)"}', $result);
    }

    /**
     * @test
     *
     * @dataProvider provideMultiDimensionalArrays
     *
     * @param array<int|string, mixed> $phpValue
     */
    public function throws_exception_for_multi_dimensional_arrays(array $phpValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        $this->expectExceptionMessage('Only single-dimensioned arrays are supported');
        PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpValue);
    }

    /**
     * @return array<string, array{phpValue: array}>
     */
    public static function provideMultiDimensionalArrays(): array
    {
        return [
            'array with nested array' => [
                'phpValue' => [
                    'nested' => ['array'],
                ],
            ],
            'array with multiple nested arrays' => [
                'phpValue' => [
                    ['array1'],
                    ['array2'],
                ],
            ],
            'deeply nested array' => [
                'phpValue' => [
                    'deeply' => [
                        'nested' => ['array'],
                    ],
                ],
            ],
        ];
    }
}
