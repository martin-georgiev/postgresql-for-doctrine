<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8RangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Int8RangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Int8RangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Int8RangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('int8range[]', $this->fixture->getName());
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
     *     phpValue: array<Int8RangeValueObject|null>|null,
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
                'phpValue' => [new Int8RangeValueObject(1, 1000)],
                'postgresValue' => '{"[1,1000)"}',
            ],
            'multiple ranges' => [
                'phpValue' => [
                    new Int8RangeValueObject(1, 1000),
                    new Int8RangeValueObject(2000, 5000),
                ],
                'postgresValue' => '{"[1,1000)","[2000,5000)"}',
            ],
            'inclusive upper bound' => [
                'phpValue' => [new Int8RangeValueObject(1, 100, true, true)],
                'postgresValue' => '{"[1,100]"}',
            ],
            'array with null item' => [
                'phpValue' => [new Int8RangeValueObject(1, 100), null],
                'postgresValue' => '{"[1,100)",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidInt8RangeArrayItemForPHPException::class);
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
        $this->expectException(InvalidInt8RangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidArrayItemInputs(): array
    {
        return [
            'array containing strings' => [['[1,1000)']],
            'array containing integers' => [[42]],
            'array containing objects' => [[new \stdClass()]],
        ];
    }
}
