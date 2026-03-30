<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\PolygonArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PolygonArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private PolygonArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new PolygonArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('polygon[]', $this->fixture->getName());
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
     *     phpValue: array<PolygonValueObject>|null,
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
            'single polygon' => [
                'phpValue' => [new PolygonValueObject('((0,0),(1,1),(2,0))')],
                'postgresValue' => '{"((0,0),(1,1),(2,0))"}',
            ],
            'multiple polygons' => [
                'phpValue' => [
                    new PolygonValueObject('((0,0),(1,1),(2,0))'),
                    new PolygonValueObject('((1.5,2.5),(3.5,4.5),(5.5,6.5))'),
                    new PolygonValueObject('((-1,-2),(-3,-4),(-5,-6))'),
                ],
                'postgresValue' => '{"((0,0),(1,1),(2,0))","((1.5,2.5),(3.5,4.5),(5.5,6.5))","((-1,-2),(-3,-4),(-5,-6))"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPolygonArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing non-value-object items' => [[1, 2, 3]],
            'invalid nested polygon' => [['((0,0),(1,1),(2,0))']],
            'mixed array (valid and invalid)' => [
                [
                    new PolygonValueObject('((0,0),(1,1),(2,0))'),
                    'invalid',
                ],
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
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
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'too few points' => ['{"((0,0))"}'],
            'non-numeric values' => ['{"((abc,0),(1,1),(2,0))"}'],
            'invalid format' => ['{"not a polygon"}'],
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
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
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
    public function throws_exception_when_invalid_polygon_format_provided(): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,polygon)');
    }

    #[Test]
    public function throws_exception_for_malformed_polygon_strings_in_database(): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue('{"(invalid,polygon)"}', $this->platform);
    }

    #[DataProvider('provideInvalidPolygonArrayItems')]
    #[Test]
    public function throws_exception_for_invalid_polygon_array_items(array $invalidArray): void
    {
        $this->expectException(InvalidPolygonArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidArray, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidPolygonArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['not-a-polygon']],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
            'mixed invalid items' => [[123, 'not-a-polygon', true]],
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
            'triangle' => [new PolygonValueObject('((0,0),(1,1),(2,0))')],
            'decimal values' => [new PolygonValueObject('((1.5,2.5),(3.5,4.5),(5.5,6.5))')],
            'negative coordinates' => [new PolygonValueObject('((-1,-2),(-3,-4),(-5,-6))')],
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
            'string polygon format' => ['((0,0),(1,1),(2,0))'],
            'invalid string' => ['invalid'],
            'integer' => [123],
            'null' => [null],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
