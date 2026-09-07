<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\CubeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCubeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube as CubeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class CubeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&Stub
     */
    private Stub $platform;

    private CubeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createStub(AbstractPlatform::class);
        $this->fixture = new CubeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('cube[]', $this->fixture->getName());
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
     *     phpValue: array<CubeValueObject>|null,
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
            'single point' => [
                'phpValue' => [CubeValueObject::point(1.0, 2.0)],
                'postgresValue' => '{"(1, 2)"}',
            ],
            'mixed points and boxes' => [
                'phpValue' => [
                    CubeValueObject::point(1.0, 2.0),
                    new CubeValueObject([3.0, 4.0], [5.0, 6.0]),
                    CubeValueObject::point(-1.5),
                ],
                'postgresValue' => '{"(1, 2)","(3, 4),(5, 6)","(-1.5)"}',
            ],
        ];
    }

    #[Test]
    public function converts_unquoted_one_dimensional_item_to_php_value(): void
    {
        // PostgreSQL does not quote a cube element that contains no comma, so a
        // one-dimensional cube comes back bare while its neighbours are quoted.
        $result = $this->fixture->convertToPHPValue('{(1),"(2, 3)",(4)}', $this->platform);

        $expected = [
            CubeValueObject::point(1.0),
            CubeValueObject::point(2.0, 3.0),
            CubeValueObject::point(4.0),
        ];
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function converts_null_item_to_php_value(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function converts_null_item_from_database_to_php_value(): void
    {
        $result = $this->fixture->convertToPHPValue('{NULL,"(1, 2)"}', $this->platform);

        $this->assertEquals([null, CubeValueObject::point(1.0, 2.0)], $result);
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }

    #[Test]
    public function throws_exception_when_invalid_cube_format_provided(): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,cube)');
    }

    #[Test]
    public function throws_exception_for_malformed_cube_strings_in_database(): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue('{"(invalid,cube)"}', $this->platform);
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'unparsable item' => ['{invalid}'],
            'empty item' => ['{""}'],
            'non-numeric coordinates' => ['{"(abc, 1)"}'],
            'multi-dimensional array' => ['{{"(1, 2)"},{"(3, 4)"}}'],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
        ];
    }

    #[DataProvider('provideInvalidPHPValueTypes')]
    #[Test]
    public function throws_exception_for_non_string_inputs_to_database_conversion(mixed $value): void
    {
        $this->expectException(InvalidCubeArrayItemForPHPException::class);
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

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCubeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing non-value-object items' => [[1, 2, 3]],
            'array containing cube strings' => [['(1, 2)']],
            'mixed array (valid and invalid)' => [
                [
                    CubeValueObject::point(1.0, 2.0),
                    'invalid',
                ],
            ],
        ];
    }

    #[DataProvider('provideInvalidCubeArrayItems')]
    #[Test]
    public function throws_exception_for_invalid_cube_array_items(array $invalidArray): void
    {
        $this->expectException(InvalidCubeArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidArray, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidCubeArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['not-a-cube']],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
            'null item' => [[null]],
            'mixed invalid items' => [[123, 'not-a-cube', true]],
        ];
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
            'point' => [CubeValueObject::point(1.0, 2.0)],
            'box' => [new CubeValueObject([1.0, 2.0], [3.0, 4.0])],
            'one-dimensional point' => [CubeValueObject::point(42.0)],
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
            'string cube format' => ['(1, 2)'],
            'invalid string' => ['invalid'],
            'integer' => [123],
            'null' => [null],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
