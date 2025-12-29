<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\CidrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CidrArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private CidrArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new CidrArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('cidr[]', $this->fixture->getName());
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
            'IPv4 CIDR array' => [
                'phpValue' => ['192.168.0.0/24', '10.0.0.0/8'],
                'postgresValue' => '{"192.168.0.0/24","10.0.0.0/8"}',
            ],
            'IPv6 CIDR array' => [
                'phpValue' => ['2001:db8::/32', 'fe80::/10'],
                'postgresValue' => '{"2001:db8::/32","fe80::/10"}',
            ],
            'mixed CIDR array' => [
                'phpValue' => ['192.168.1.0/24', '2001:db8::/32'],
                'postgresValue' => '{"192.168.1.0/24","2001:db8::/32"}',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'invalid IPv4' => [['256.256.256.0/24']],
            'invalid IPv6' => [['2001:xyz::/32']],
            'missing netmask' => [['192.168.1.0']],
            'invalid netmask IPv4' => [['192.168.1.0/33']],
            'invalid netmask IPv6' => [['2001:db8::/129']],
            'mixed valid and invalid' => [['192.168.1.0/24', '256.256.256.0/24']],
            'empty string' => [['']],
            'whitespace only' => [[' ']],
            'malformed CIDR with spaces' => [['192.168.1.0 / 24']],
            'valid value mixed with null array item' => [['192.168.1.0/24', null]],
            'valid value mixed with integer array item' => [['192.168.1.0/24', 123]],
            'valid value mixed with boolean array item' => [['192.168.1.0/24', true]],
            'valid value mixed with object array item' => [['192.168.1.0/24', new \stdClass()]],
        ];
    }

    #[DataProvider('provideInvalidTypeInputs')]
    #[Test]
    public function throws_exception_for_invalid_type_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
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
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'invalid format' => ['{"invalid-cidr"}'],
            'invalid CIDR in array' => ['{"256.256.256.0/24"}'],
            'malformed array' => ['not-an-array'],
            'empty item in array' => ['{"192.168.1.0/24",""}'],
            'invalid item in array' => ['{"192.168.1.0/24","invalid-cidr"}'],
            'missing netmask in array' => ['{"192.168.1.0"}'],
            'invalid netmask in array' => ['{"192.168.1.0/33"}'],
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
            'IPv4 CIDR' => ['192.168.1.0/24'],
            'IPv6 CIDR' => ['2001:db8::/32'],
            'IPv4 host CIDR' => ['10.0.0.1/32'],
            'IPv6 host CIDR' => ['::1/128'],
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
            'invalid CIDR' => ['invalid-cidr'],
            'IP without netmask' => ['192.168.1.0'],
            'integer' => [123],
            'null' => [null],
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
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
        $this->fixture->transformArrayItemForPHP(123);
    }
}
