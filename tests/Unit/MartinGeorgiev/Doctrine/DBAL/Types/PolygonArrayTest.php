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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class PolygonArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private PolygonArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new PolygonArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('polygon[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array<PolygonValueObject|null>|null,
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
                'phpValue' => [PolygonValueObject::fromString('((0,0),(1,1),(2,0))')],
                'postgresValue' => '{"((0,0),(1,1),(2,0))"}',
            ],
            'multiple polygons' => [
                'phpValue' => [
                    PolygonValueObject::fromString('((0,0),(1,1),(2,0))'),
                    PolygonValueObject::fromString('((1.5,2.5),(3.5,4.5),(5.5,6.5))'),
                    PolygonValueObject::fromString('((-1,-2),(-3,-4),(-5,-6))'),
                ],
                'postgresValue' => '{"((0,0),(1,1),(2,0))","((1.5,2.5),(3.5,4.5),(5.5,6.5))","((-1,-2),(-3,-4),(-5,-6))"}',
            ],
            'array with null element' => [
                'phpValue' => [PolygonValueObject::fromString('((0,0),(1,1),(2,0))'), null],
                'postgresValue' => '{"((0,0),(1,1),(2,0))",NULL}',
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
                    PolygonValueObject::fromString('((0,0),(1,1),(2,0))'),
                    'invalid',
                ],
            ],
            'array containing a boolean' => [[true]],
            'array containing a plain object' => [[new \stdClass()]],
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
            'integer instead of array' => [123],
            'object instead of array' => [new \stdClass()],
            'boolean instead of array' => [true],
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
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }

    #[DataProvider('provideMalformedInputs')]
    #[Test]
    public function throws_exception_for_malformed_array_literal(string $postgresValue): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedInputs(): array
    {
        return [
            'unparsable item' => ['{invalid}'],
            'quoted empty item' => ['{""}'],
            'not an array literal' => ['not-an-array'],
        ];
    }

    #[Test]
    public function throws_exception_when_invalid_polygon_format_provided(): void
    {
        $this->expectException(InvalidPolygonArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,polygon)');
    }

    #[DataProvider('provideValidArrayItemsForDatabase')]
    #[Test]
    public function validates_valid_array_item_for_database(mixed $value): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($value));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideValidArrayItemsForDatabase(): array
    {
        return [
            'triangle' => [PolygonValueObject::fromString('((0,0),(1,1),(2,0))')],
            'decimal values' => [PolygonValueObject::fromString('((1.5,2.5),(3.5,4.5),(5.5,6.5))')],
            'negative coordinates' => [PolygonValueObject::fromString('((-1,-2),(-3,-4),(-5,-6))')],
            'null' => [null],
        ];
    }

    #[DataProvider('provideInvalidArrayItemsForDatabase')]
    #[Test]
    public function validates_invalid_array_item_for_database(mixed $value): void
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
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
