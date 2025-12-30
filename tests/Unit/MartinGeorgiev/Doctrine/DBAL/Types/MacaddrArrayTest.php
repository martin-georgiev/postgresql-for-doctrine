<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MacaddrArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private MacaddrArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new MacaddrArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('macaddr[]', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
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
            'colon-separated MAC addresses' => [
                'phpValue' => ['08:00:2b:01:02:03', '00:0c:29:aa:bb:cc'],
                'postgresValue' => '{"08:00:2b:01:02:03","00:0c:29:aa:bb:cc"}',
            ],
            'hyphen-separated MAC addresses' => [
                'phpValue' => ['08-00-2b-01-02-03', '00-0c-29-aa-bb-cc'],
                'postgresValue' => '{"08-00-2b-01-02-03","00-0c-29-aa-bb-cc"}',
            ],
            'mixed separator MAC addresses' => [
                'phpValue' => ['08:00:2b:01:02:03', '00-0c-29-aa-bb-cc'],
                'postgresValue' => '{"08:00:2b:01:02:03","00-0c-29-aa-bb-cc"}',
            ],
            'uppercase MAC addresses' => [
                'phpValue' => ['08:00:2B:01:02:03', '00:0C:29:AA:BB:CC'],
                'postgresValue' => '{"08:00:2B:01:02:03","00:0C:29:AA:BB:CC"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'invalid MAC format' => [['00:11:22:33:44:ZZ']],
            'too short' => [['00:11:22:33:44']],
            'too long' => [['00:11:22:33:44:55:66']],
            'no separators' => [['000011223344']],
            'wrong separator' => [['00.11.22.33.44.55']],
            'non-hex characters' => [['GG:HH:II:JJ:KK:LL']],
            'mixed valid and invalid' => [['08:00:2b:01:02:03', 'invalid-mac']],
            'empty string' => [['']],
            'whitespace only' => [[' ']],
            'malformed with spaces' => [['08:00 :2b:01:02:03']],
            'mixed case invalid chars' => [['GG:hh:II:jj:KK:ll']],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);
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
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format' => ['{"invalid-mac"}'],
            'invalid MAC in array' => ['{"00:11:22:33:44:ZZ"}'],
            'malformed array' => ['not-an-array'],
            'empty item in array' => ['{"08:00:2b:01:02:03",""}'],
            'invalid item in array' => ['{"08:00:2b:01:02:03","invalid-mac"}'],
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
            'colon separated lowercase' => ['08:00:2b:01:02:03'],
            'hyphen separated lowercase' => ['08-00-2b-01-02-03'],
            'colon separated mixed case' => ['00:0c:29:Aa:Bb:CC'],
            'all zeros' => ['00:00:00:00:00:00'],
            'all ones' => ['ff:ff:ff:ff:ff:ff'],
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
            'invalid MAC' => ['invalid-mac'],
            'too short' => ['00:11:22:33:44'],
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
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
