<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PHPArrayToPostgresValueTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PHPArrayToPostgresValueTransformerTest extends TestCase
{
    /**
     * @param array<int, array<string, array|string>> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertEquals($postgresValue, PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpValue));
    }

    /**
     * @return array<string, array{phpValue: array, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
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
            'boolean values' => [
                'phpValue' => [
                    0 => true,
                    1 => false,
                ],
                'postgresValue' => '{true,false}',
            ],
            'null values' => [
                'phpValue' => [
                    0 => null,
                    1 => 'not null',
                    2 => null,
                ],
                'postgresValue' => '{NULL,"not null",NULL}',
            ],
            'empty string' => [
                'phpValue' => [
                    0 => '',
                    1 => 'not empty',
                ],
                'postgresValue' => '{"","not empty"}',
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
            'with only nulls' => [
                'phpValue' => [null, null],
                'postgresValue' => '{NULL,NULL}',
            ],
            'with only booleans' => [
                'phpValue' => [true, false, true],
                'postgresValue' => '{true,false,true}',
            ],
            'with empty empty strings' => [
                'phpValue' => ['', ''],
                'postgresValue' => '{"",""}',
            ],
        ];
    }

    /**
     * @param array<int, mixed> $phpValue
     */
    #[DataProvider('provideInvalidPHPValuesForDatabaseTransformation')]
    #[Test]
    public function throws_invalid_argument_exception_when_tries_to_non_single_dimensioned_array_from_php_value(array $phpValue): void
    {
        $this->expectException(InvalidArrayFormatException::class);
        PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($phpValue);
    }

    /**
     * @return array<string, array{phpValue: array}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
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
            ],
        ];
    }

    #[Test]
    public function can_transform_object_with_to_string_method(): void
    {
        $object = new class {
            public function __toString(): string
            {
                return 'object string representation';
            }
        };

        $this->assertSame('{"object string representation"}', PHPArrayToPostgresValueTransformer::transformToPostgresTextArray([$object]));
    }

    #[Test]
    public function can_transform_object_without_to_string_method(): void
    {
        $object = new class {};

        // Should contain the class name
        $this->assertStringContainsString('class@anonymous', PHPArrayToPostgresValueTransformer::transformToPostgresTextArray([$object]));
    }

    #[Test]
    public function can_transform_closed_resource(): void
    {
        $resource = \fopen('php://temp', 'r');
        \assert(\is_resource($resource));
        \fclose($resource);

        $this->assertSame('{"resource (closed)"}', PHPArrayToPostgresValueTransformer::transformToPostgresTextArray([$resource]));
    }

    #[Test]
    public function can_transform_open_resource(): void
    {
        $resource = \fopen('php://temp', 'r');
        \assert(\is_resource($resource));

        $this->assertSame('{"(resource)"}', PHPArrayToPostgresValueTransformer::transformToPostgresTextArray([$resource]));
    }

    #[Test]
    public function can_transform_mixed_types_in_array(): void
    {
        $input = [
            'string',
            123,
            1.5,
            true,
            null,
            new class {
                public function __toString(): string
                {
                    return 'object';
                }
            },
            '',
        ];

        $this->assertEquals('{"string",123,1.5,true,NULL,"object",""}', PHPArrayToPostgresValueTransformer::transformToPostgresTextArray($input));
    }

    /**
     * @param array<int|string, mixed> $phpValue
     */
    #[DataProvider('provideMultiDimensionalArrays')]
    #[Test]
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

    #[Test]
    public function can_transform_array_with_gd_resource(): void
    {
        if (!\function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension not available');
        }

        $resource = \imagecreatetruecolor(1, 1);
        $result = PHPArrayToPostgresValueTransformer::transformToPostgresTextArray([$resource]);
        $this->assertStringContainsString('GdImage', $result);
    }
}
