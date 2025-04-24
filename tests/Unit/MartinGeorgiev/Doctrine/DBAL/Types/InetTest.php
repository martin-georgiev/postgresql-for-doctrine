<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Inet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InetTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Inet $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Inet();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('inet', $this->fixture->getName());
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
            'IPv4 address' => [
                'phpValue' => '192.168.0.1',
                'postgresValue' => '192.168.0.1',
            ],
            'IPv4 with CIDR' => [
                'phpValue' => '192.168.0.0/24',
                'postgresValue' => '192.168.0.0/24',
            ],
            'IPv6 address' => [
                'phpValue' => '2001:db8::1',
                'postgresValue' => '2001:db8::1',
            ],
            'IPv6 with CIDR' => [
                'phpValue' => '2001:db8::/32',
                'postgresValue' => '2001:db8::/32',
            ],
            'IPv4 loopback' => [
                'phpValue' => '127.0.0.1',
                'postgresValue' => '127.0.0.1',
            ],
            'IPv6 loopback' => [
                'phpValue' => '::1',
                'postgresValue' => '::1',
            ],
            'IPv4 with leading zeros' => [
                'phpValue' => '192.168.001.001',
                'postgresValue' => '192.168.001.001',
            ],
            'IPv6 compressed zeros' => [
                'phpValue' => '2001:db8::1',
                'postgresValue' => '2001:db8::1',
            ],
            'IPv6 full notation' => [
                'phpValue' => '2001:0db8:0000:0000:0000:0000:0000:0001',
                'postgresValue' => '2001:0db8:0000:0000:0000:0000:0000:0001',
            ],
            'IPv4-mapped IPv6' => [
                'phpValue' => '::ffff:192.168.1.1',
                'postgresValue' => '::ffff:192.168.1.1',
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
        $this->expectException(InvalidInetForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            'non-string value' => [123],
            'empty string' => [''],
            'invalid IPv4' => ['256.256.256.256'],
            'invalid IPv6' => ['2001:xyz::1'],
            'invalid CIDR format' => ['192.168.1.0/xyz'],
            'invalid IPv4 CIDR netmask' => ['192.168.1.0/33'],
            'invalid IPv6 CIDR netmask' => ['2001:db8::/129'],
            'wrong format' => ['not-an-ip-address'],
            'incomplete IPv4' => ['192.168.1'],
            'incomplete IPv6' => ['2001:db8'],
            'IPv6 too many segments' => ['2001:db8:1:2:3:4:5:6:7'],
            'IPv6 invalid segment length' => ['2001:db8:xyz:1:1:1:1:1'],
            'IPv4 with invalid octet count' => ['192.168.1'],
            'IPv4 with character suffix' => ['192.168.1.1x'],
            'malformed IPv4-mapped IPv6' => ['::ffff:256.256.256.256'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValuesForPHPTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(mixed $postgresValue): void
    {
        $this->expectException(InvalidInetForDatabaseException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValuesForPHPTransformation(): array
    {
        return [
            'non-string value' => [123],
            'invalid IPv4' => ['256.256.256.256'],
            'invalid IPv6' => ['2001:xyz::1'],
            'invalid CIDR format' => ['192.168.1.0/xyz'],
            'invalid IPv4 CIDR netmask' => ['192.168.1.0/33'],
            'invalid IPv6 CIDR netmask' => ['2001:db8::/129'],
            'wrong format' => ['not-an-ip-address'],
            'incomplete IPv4' => ['192.168.1'],
            'incomplete IPv6' => ['2001:db8'],
            'IPv6 too many segments' => ['2001:db8:1:2:3:4:5:6:7'],
            'IPv6 invalid segment length' => ['2001:db8:xyz:1:1:1:1:1'],
            'IPv4 with invalid octet count' => ['192.168.1'],
            'IPv4 with character suffix' => ['192.168.1.1x'],
            'malformed IPv4-mapped IPv6' => ['::ffff:256.256.256.256'],
        ];
    }
}
