<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8MultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8MultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8MultirangeArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Int8MultirangeArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Int8MultirangeArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Int8MultirangeArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('int8multirange[]', $this->fixture->getName());
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
     *     phpValue: array<Int8MultirangeValueObject|null>|null,
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
            'single multirange' => [
                'phpValue' => [new Int8MultirangeValueObject([new Int8Range(1, 100)])],
                'postgresValue' => '{"{[1,100)}"}',
            ],
            'multirange with two ranges' => [
                'phpValue' => [new Int8MultirangeValueObject([new Int8Range(1, 50), new Int8Range(100, 200)])],
                'postgresValue' => '{"{[1,50),[100,200)}"}',
            ],
            'empty multirange item' => [
                'phpValue' => [new Int8MultirangeValueObject([])],
                'postgresValue' => '{"{}"}',
            ],
            'multiple multiranges' => [
                'phpValue' => [
                    new Int8MultirangeValueObject([new Int8Range(1, 50)]),
                    new Int8MultirangeValueObject([new Int8Range(100, 200)]),
                ],
                'postgresValue' => '{"{[1,50)}","{[100,200)}"}',
            ],
            'array with null item' => [
                'phpValue' => [new Int8MultirangeValueObject([new Int8Range(1, 50)]), null],
                'postgresValue' => '{"{[1,50)}",NULL}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidInt8MultirangeArrayItemForDatabaseException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'array containing string' => [['not-a-multirange']],
            'array containing integer' => [[42]],
            'array containing object' => [[new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidInt8MultirangeArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTypeInputs(): array
    {
        return [
            'string instead of array' => ['not-an-array'],
            'integer instead of array' => [42],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidInt8MultirangeArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format in array' => ['{"not-a-multirange"}'],
        ];
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidInt8MultirangeArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(42);
    }
}
