<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Cidr;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForPHPException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CidrTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Cidr $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Cidr();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('cidr', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(?string $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(?string $phpValue, ?string $postgresValue): void
    {
        self::assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }

    /**
     * @return array<string, array{phpValue: string|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            'IPv4 CIDR' => [
                'phpValue' => '192.168.0.0/24',
                'postgresValue' => '192.168.0.0/24',
            ],
            'IPv4 CIDR with min netmask' => [
                'phpValue' => '10.0.0.0/0',
                'postgresValue' => '10.0.0.0/0',
            ],
            'IPv4 CIDR with max netmask' => [
                'phpValue' => '172.16.0.0/32',
                'postgresValue' => '172.16.0.0/32',
            ],
            'IPv6 CIDR' => [
                'phpValue' => '2001:db8::/32',
                'postgresValue' => '2001:db8::/32',
            ],
            'IPv6 CIDR with min netmask' => [
                'phpValue' => 'fe80::/0',
                'postgresValue' => 'fe80::/0',
            ],
            'IPv6 CIDR with max netmask' => [
                'phpValue' => '2001:db8::/128',
                'postgresValue' => '2001:db8::/128',
            ],
            'IPv6 CIDR uppercase' => [
                'phpValue' => '2001:DB8::/32',
                'postgresValue' => '2001:DB8::/32',
            ],
            'IPv6 CIDR full notation' => [
                'phpValue' => '2001:0db8:0000:0000:0000:0000:0000:0000/32',
                'postgresValue' => '2001:0db8:0000:0000:0000:0000:0000:0000/32',
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
        $this->expectException(InvalidCidrForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            'empty string' => [''],
            'whitespace string' => [' '],
            'plain IPv4' => ['192.168.0.1'],
            'plain IPv6' => ['2001:db8::1'],
            'invalid type' => [123],
            'malformed CIDR with spaces' => ['192.168.0.0 / 24'],
            'invalid IPv4 address' => ['256.256.256.0/24'],
            'invalid IPv6 address' => ['2001:xyz::/32'],
            'IPv4 invalid netmask low' => ['192.168.0.0/-1'],
            'IPv4 invalid netmask high' => ['192.168.0.0/33'],
            'IPv6 invalid netmask low' => ['2001:db8::/-1'],
            'IPv6 invalid netmask high' => ['2001:db8::/129'],
            'CIDR without netmask' => ['192.168.0.0/'],
            'CIDR with non-numeric netmask' => ['192.168.0.0/xyz'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValuesForPHPTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(mixed $dbValue): void
    {
        $this->expectException(InvalidCidrForDatabaseException::class);
        $this->fixture->convertToPHPValue($dbValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValuesForPHPTransformation(): array
    {
        return [
            'invalid type' => [123],
            'invalid format from database' => ['invalid-cidr-format'],
        ];
    }
}
