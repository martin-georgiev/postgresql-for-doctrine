<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\LineArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LineArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private LineArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new LineArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('line[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<LineValueObject>|null,
     *     postgresValue: string|null
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'empty array' => [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            'single line' => [
                'phpValue' => [LineValueObject::fromString('{1,0,0}')],
                'postgresValue' => '{"{1,0,0}"}',
            ],
            'multiple lines' => [
                'phpValue' => [
                    LineValueObject::fromString('{1,0,0}'),
                    LineValueObject::fromString('{1.5,2.5,3.5}'),
                    LineValueObject::fromString('{-1,-2,-3}'),
                ],
                'postgresValue' => '{"{1,0,0}","{1.5,2.5,3.5}","{-1,-2,-3}"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLineArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing non-value-object items' => [[1, 2, 3]],
            'invalid nested line' => [['{1,0,0}']],
            'mixed array (valid and invalid)' => [
                [
                    LineValueObject::fromString('{1,0,0}'),
                    'invalid',
                ],
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['string value'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'missing braces' => ['{"1,0,0"}'],
            'non-numeric values' => ['{"{abc,0,0}"}'],
            'invalid format' => ['{"not a line"}'],
        ];
    }

    #[Test]
    public function can_transform_array_item_for_php_returning_null_for_null(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function can_handle_edge_case_with_empty_and_malformed_arrays(): void
    {
        $result1 = $this->fixture->convertToPHPValue('{}', $this->platform);
        $result2 = $this->fixture->convertToPHPValue('{invalid}', $this->platform);
        $result3 = $this->fixture->convertToPHPValue('{""}', $this->platform);

        $this->assertSame([], $result1);
        $this->assertSame([], $result2);
        $this->assertSame([], $result3);
    }

    #[DataProvider('provideInvalidPHPValueTypes')]
    #[Test]
    public function throws_exception_for_non_string_inputs_to_database_conversion(mixed $value): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($value, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueTypes(): array
    {
        return [
            'integer' => [123],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function throws_exception_when_invalid_line_format_provided(): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,line)');
    }

    #[Test]
    public function throws_exception_for_malformed_line_strings_in_database(): void
    {
        $this->expectException(InvalidLineArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue('{"(invalid,line)"}', $this->platform);
    }

    #[DataProvider('provideInvalidLineArrayItems')]
    #[Test]
    public function throws_exception_for_invalid_line_array_items(array $invalidArray): void
    {
        $this->expectException(InvalidLineArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidArray, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidLineArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['not-a-line']],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
            'mixed invalid items' => [[123, 'not-a-line', true]],
        ];
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'standard line' => [LineValueObject::fromString('{1,0,0}')],
            'decimal values' => [LineValueObject::fromString('{1.5,2.5,3.5}')],
            'negative coefficients' => [LineValueObject::fromString('{-1,-2,-3}')],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function can_validate_invalid_array_item_for_database(mixed $value): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemsForDatabase(): array
    {
        return [
            'string line format' => ['{1,0,0}'],
            'invalid string' => ['invalid'],
            'integer' => [123],
            'null' => [null],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
