<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PostgresArrayToPHPArrayTransformerTest extends TestCase
{
    /**
     * @param array<int, array<string, array|string>> $phpValue
     */
    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(array $phpValue, string $postgresValue): void
    {
        $this->assertEquals($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue));
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
            'only whitespace' => [
                'phpValue' => [],
                'postgresValue' => '   ',
            ],
            'with trailing comma' => [
                'phpValue' => ['a'],
                'postgresValue' => '{a,}}',
            ],
            'with only backslashes' => [
                'phpValue' => ['\\'],
                'postgresValue' => '{"\\\\"}',
            ],
            'with only double quotes' => [
                'phpValue' => ['"'],
                'postgresValue' => '{"\""}',
            ],
            'with empty quoted strings' => [
                'phpValue' => ['', ''],
                'postgresValue' => '{"",""}',
            ],
            'github #351 regression #1: string with special characters and backslash' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./'],
                'postgresValue' => '{"⥀!@#$%^&*()_+=-}{[]|\":;\'\\?><,./"}',
            ],
            'github #351 regression #2: string with special characters, backslash and additional element' => [
                'phpValue' => ['⥀!@#$%^&*()_+=-}{[]|":;\'\?><,./', 'text'],
                'postgresValue' => '{"⥀!@#$%^&*()_+=-}{[]|\":;\'\\?><,./",text}',
            ],
            'backslash before backslash' => [
                'phpValue' => ['a\b'],
                'postgresValue' => '{"a\\\b"}', // a\\b
            ],
            'single backslash before non-escape char' => [
                'phpValue' => ['a\$b'],
                'postgresValue' => '{"a\$b"}', // a\$b
            ],
            'element with curly braces and comma' => [
                'phpValue' => ['{foo,bar}'],
                'postgresValue' => '{"{foo,bar}"}',
            ],
            'element with whitespace' => [
                'phpValue' => ['  foo  '],
                'postgresValue' => '{"  foo  "}',
            ],
            'github #424 regression: numeric strings should be preserved as strings when unquoted' => [
                'phpValue' => ['1', 'test', 'true'],
                'postgresValue' => '{1,test,true}',
            ],
        ];
    }

    #[DataProvider('provideMultiDimensionalArrays')]
    #[Test]
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

    #[DataProvider('provideManualParsingArrays')]
    #[Test]
    public function can_recover_from_json_decode_failure_and_transform_value_through_manual_parsing(array $phpValue, string $postgresValue): void
    {
        $this->assertSame($phpValue, PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue));
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

    #[Test]
    public function can_transform_escaped_quotes_with_backslashes(): void
    {
        $postgresArray = '{"\\\"quoted\\\""}';
        $this->assertSame(['\"quoted\"'], PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray));
    }

    #[Test]
    public function can_preserves_numeric_precision(): void
    {
        $postgresArray = '{"9223372036854775808","1.23456789012345"}';
        $this->assertSame(['9223372036854775808', '1.23456789012345'], PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray));
    }

    #[DataProvider('provideInvalidPostgresArrays')]
    #[Test]
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

    #[DataProvider('providePreserveStringTypesTestCases')]
    #[Test]
    public function can_preserve_string_types_when_requested(array $expectedPhpValue, string $postgresValue): void
    {
        $result = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresValue, preserveStringTypes: true);

        $this->assertSame($expectedPhpValue, $result);

        // Verify all values are strings when preserveStringTypes is true
        foreach ($result as $value) {
            $this->assertIsString($value, \sprintf('All values should be strings when preserveStringTypes is true, but found a non-string value: %s', \var_export($value, true)));
        }
    }

    /**
     * @return array<string, array{expectedPhpValue: array, postgresValue: string}>
     */
    public static function providePreserveStringTypesTestCases(): array
    {
        return [
            'floats with trailing zeros - issue #482' => [
                'expectedPhpValue' => ['502.00', '505.00', '123.50'],
                'postgresValue' => '{502.00,505.00,123.50}',
            ],
            'zero with decimals' => [
                'expectedPhpValue' => ['0.00', '0.0', '0.000'],
                'postgresValue' => '{0.00,0.0,0.000}',
            ],
            'mixed numeric-looking and text values' => [
                'expectedPhpValue' => ['502.00', 'some text', '123.50', 'another'],
                'postgresValue' => '{502.00,some text,123.50,another}',
            ],
            'scientific notation as strings' => [
                'expectedPhpValue' => ['1.23e10', '4.56E-5', '7.89e+3'],
                'postgresValue' => '{1.23e10,4.56E-5,7.89e+3}',
            ],
            'already quoted values with decimals' => [
                'expectedPhpValue' => ['502.00', '123.50'],
                'postgresValue' => '{"502.00","123.50"}',
            ],
            'mixed quoted and unquoted with decimals' => [
                'expectedPhpValue' => ['502.00', '123.50', 'text', '789.00'],
                'postgresValue' => '{502.00,"123.50",text,"789.00"}',
            ],
            'integers should remain as strings' => [
                'expectedPhpValue' => ['1', '2', '3', '100'],
                'postgresValue' => '{1,2,3,100}',
            ],
            'boolean-like values as strings' => [
                'expectedPhpValue' => ['true', 'false', 't', 'f'],
                'postgresValue' => '{true,false,t,f}',
            ],
            'null values as strings' => [
                'expectedPhpValue' => ['null', 'NULL'],
                'postgresValue' => '{null,NULL}',
            ],
            'empty strings preserved' => [
                'expectedPhpValue' => ['', 'text', ''],
                'postgresValue' => '{"",text,""}',
            ],
        ];
    }
}
