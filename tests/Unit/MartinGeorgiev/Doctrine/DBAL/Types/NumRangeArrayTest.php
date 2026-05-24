<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\NumRangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumericRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NumRangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private NumRangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new NumRangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('numrange[]', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_null(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
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
     *     phpValue: array<NumericRangeValueObject|null>|null,
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
            'single range' => [
                'phpValue' => [new NumericRangeValueObject(1, 10)],
                'postgresValue' => '{"[1,10)"}',
            ],
            'multiple ranges' => [
                'phpValue' => [
                    new NumericRangeValueObject(1, 10),
                    new NumericRangeValueObject(20, 30),
                ],
                'postgresValue' => '{"[1,10)","[20,30)"}',
            ],
            'float bounds' => [
                'phpValue' => [new NumericRangeValueObject(1.5, 9.9)],
                'postgresValue' => '{"[1.5,9.9)"}',
            ],
            'array with null item' => [
                'phpValue' => [new NumericRangeValueObject(1, 10), null],
                'postgresValue' => '{"[1,10)",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumRangeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
            'boolean instead of array' => [false],
        ];
    }

    #[DataProvider('provideInvalidArrayItemInputs')]
    #[Test]
    public function throws_exception_for_invalid_array_item_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidNumRangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemInputs(): array
    {
        return [
            'array containing strings' => [['[1,10)']],
            'array containing integers' => [[42]],
            'array containing objects' => [[new \stdClass()]],
        ];
    }
}
