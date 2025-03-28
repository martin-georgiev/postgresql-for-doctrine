<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Cidr;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForPHPException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CidrTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private AbstractPlatform $platform;

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
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_database_value(mixed $phpValue): void
    {
        $this->expectException(InvalidCidrForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            'empty string' => [''],
            'invalid IPv4' => ['256.256.256.0/24'],
            'invalid IPv6' => ['2001:xyz::/32'],
            'missing netmask' => ['192.168.1.0'],
            'invalid netmask format' => ['192.168.1.0/xyz'],
            'invalid netmask IPv4 too large' => ['192.168.1.0/33'],
            'invalid netmask IPv4 negative' => ['192.168.1.0/-1'],
            'invalid netmask IPv6 too large' => ['2001:db8::/129'],
            'invalid netmask IPv6 negative' => ['2001:db8::/-1'],
            'wrong format' => ['not-a-cidr'],
        ];
    }
}
