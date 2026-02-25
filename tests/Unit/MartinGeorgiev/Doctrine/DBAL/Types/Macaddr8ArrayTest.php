<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8Array;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Macaddr8ArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Macaddr8Array $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Macaddr8Array();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('macaddr8[]', $this->fixture->getName());
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
        $this->assertSame($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{
     *     phpValue: array|null,
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
            'colon-separated EUI-64 addresses' => [
                'phpValue' => ['08:00:2b:ff:fe:01:02:03', '08:00:2b:ff:fe:04:05:06'],
                'postgresValue' => '{"08:00:2b:ff:fe:01:02:03","08:00:2b:ff:fe:04:05:06"}',
            ],
            'hyphen-separated EUI-64 addresses' => [
                'phpValue' => ['08-00-2b-ff-fe-01-02-03', '08-00-2b-ff-fe-04-05-06'],
                'postgresValue' => '{"08-00-2b-ff-fe-01-02-03","08-00-2b-ff-fe-04-05-06"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'too short (6-octet macaddr)' => [['08:00:2b:01:02:03']],
            'too long' => [['08:00:2b:ff:fe:01:02:03:04']],
            'invalid hex chars' => [['08:00:2b:zz:fe:01:02:03']],
            'empty string' => [['']],
            'mixed valid and invalid' => [['08:00:2b:ff:fe:01:02:03', 'invalid']],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);
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

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(string $postgresValue): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format' => ['{"invalid-mac8"}'],
            'too short in array' => ['{"08:00:2b:01:02:03"}'],
            'malformed array' => ['not-an-array'],
        ];
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
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
            'colon-separated lowercase' => ['08:00:2b:ff:fe:01:02:03'],
            'hyphen-separated lowercase' => ['08-00-2b-ff-fe-01-02-03'],
            'dot notation' => ['0800.2bff.fe01.0203'],
            'no separator uppercase' => ['08002BFFFE010203'],
            'all zeros' => ['00:00:00:00:00:00:00:00'],
            'null' => [null],
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
            'too short (6-octet)' => ['08:00:2b:01:02:03'],
            'invalid chars' => ['08:00:2b:zz:fe:01:02:03'],
            'integer' => [123],
            'empty string' => [''],
            'boolean' => [true],
        ];
    }

    #[Test]
    public function can_transform_null_item_for_php(): void
    {
        $this->assertNull($this->fixture->transformArrayItemForPHP(null));
    }

    #[Test]
    public function throws_exception_for_non_string_item_from_database(): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
