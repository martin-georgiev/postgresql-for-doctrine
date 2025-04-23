<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\CidrArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrArrayItemForPHPException;
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

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('cidr[]', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: array|null, postgresValue: string|null}>
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

    /**
     * @test
     *
     * @dataProvider provideInvalidPHPValuesForDatabaseTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            'invalid type' => ['not-an-array'],
            'invalid IPv4' => [['256.256.256.0/24']],
            'invalid IPv6' => [['2001:xyz::/32']],
            'missing netmask' => [['192.168.1.0']],
            'invalid netmask IPv4' => [['192.168.1.0/33']],
            'invalid netmask IPv6' => [['2001:db8::/129']],
            'mixed valid and invalid' => [['192.168.1.0/24', '256.256.256.0/24']], // Mixed valid/invalid values
            'empty string' => [['']], // Empty string in array
            'whitespace only' => [[' ']], // Whitespace string in array
            'malformed CIDR with spaces' => [['192.168.1.0 / 24']], // Space in CIDR notation
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValuesForPHPTransformationForPHPTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(string $postgresValue): void
    {
        $this->expectException(InvalidCidrArrayItemForPHPException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidDatabaseValuesForPHPTransformationForPHPTransformation(): array
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
}
