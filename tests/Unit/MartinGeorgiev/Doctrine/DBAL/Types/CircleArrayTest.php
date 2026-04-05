<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\CircleArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CircleArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private CircleArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new CircleArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('circle[]', $this->fixture->getName());
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
     *     phpValue: array<CircleValueObject>|null,
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
            'single circle' => [
                'phpValue' => [CircleValueObject::fromString('<(0,0),1>')],
                'postgresValue' => '{"<(0,0),1>"}',
            ],
            'multiple circles' => [
                'phpValue' => [
                    CircleValueObject::fromString('<(0,0),1>'),
                    CircleValueObject::fromString('<(1.5,2.5),3.5>'),
                    CircleValueObject::fromString('<(-10,-20),5>'),
                ],
                'postgresValue' => '{"<(0,0),1>","<(1.5,2.5),3.5>","<(-10,-20),5>"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCircleArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing non-value-object items' => [[1, 2, 3]],
            'invalid nested circle' => [['<(1,2),3>']],
            'mixed array (valid and invalid)' => [
                [
                    CircleValueObject::fromString('<(0,0),1>'),
                    'invalid',
                ],
            ],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCircleArrayItemForPHPException::class);
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
        $this->expectException(InvalidCircleArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'missing angle brackets' => ['{"(0,0),1"}'],
            'non-numeric values' => ['{"<(abc,0),1>"}'],
            'invalid format' => ['{"not a circle"}'],
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
        $this->expectException(InvalidCircleArrayItemForPHPException::class);
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
    public function throws_exception_when_invalid_circle_format_provided(): void
    {
        $this->expectException(InvalidCircleArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP('(invalid,circle)');
    }

    #[Test]
    public function throws_exception_for_malformed_circle_strings_in_database(): void
    {
        $this->expectException(InvalidCircleArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue('{"(invalid,circle)"}', $this->platform);
    }

    #[DataProvider('provideInvalidCircleArrayItems')]
    #[Test]
    public function throws_exception_for_invalid_circle_array_items(array $invalidArray): void
    {
        $this->expectException(InvalidCircleArrayItemForDatabaseException::class);

        $this->fixture->convertToDatabaseValue($invalidArray, $this->platform);
    }

    /**
     * @return array<string, array{array}>
     */
    public static function provideInvalidCircleArrayItems(): array
    {
        return [
            'integer item' => [[123]],
            'string item' => [['not-a-circle']],
            'boolean item' => [[true]],
            'object item' => [[new \stdClass()]],
            'mixed invalid items' => [[123, 'not-a-circle', true]],
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
            'standard circle' => [CircleValueObject::fromString('<(0,0),1>')],
            'decimal values' => [CircleValueObject::fromString('<(1.5,2.5),3.5>')],
            'negative coordinates' => [CircleValueObject::fromString('<(-10,-20),5>')],
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
            'string circle format' => ['<(0,0),1>'],
            'invalid string' => ['invalid'],
            'integer' => [123],
            'null' => [null],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }
}
