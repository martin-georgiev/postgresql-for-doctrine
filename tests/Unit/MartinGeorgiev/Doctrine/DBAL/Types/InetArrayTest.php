<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\InetArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InetArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private InetArray $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new InetArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('inet[]', $this->fixture->getName());
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
            'IPv4 addresses' => [
                'phpValue' => ['192.168.0.1', '10.0.0.1'],
                'postgresValue' => '{"192.168.0.1","10.0.0.1"}',
            ],
            'IPv4 with CIDR' => [
                'phpValue' => ['192.168.0.0/24', '10.0.0.0/8'],
                'postgresValue' => '{"192.168.0.0/24","10.0.0.0/8"}',
            ],
            'IPv6 addresses' => [
                'phpValue' => ['2001:db8::1', 'fe80::1'],
                'postgresValue' => '{"2001:db8::1","fe80::1"}',
            ],
            'IPv6 with CIDR' => [
                'phpValue' => ['2001:db8::/32', 'fe80::/10'],
                'postgresValue' => '{"2001:db8::/32","fe80::/10"}',
            ],
            'mixed addresses' => [
                'phpValue' => ['192.168.1.1', '2001:db8::1'],
                'postgresValue' => '{"192.168.1.1","2001:db8::1"}',
            ],
            'mixed with CIDR' => [
                'phpValue' => ['192.168.0.0/24', '2001:db8::/32'],
                'postgresValue' => '{"192.168.0.0/24","2001:db8::/32"}',
            ],
            'uppercase IPv6' => [
                'phpValue' => ['2001:DB8::1', 'FE80::1'],
                'postgresValue' => '{"2001:DB8::1","FE80::1"}',
            ],
            'IPv6 full notation' => [
                'phpValue' => ['2001:0db8:0000:0000:0000:0000:0000:0001'],
                'postgresValue' => '{"2001:0db8:0000:0000:0000:0000:0000:0001"}',
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
        $this->expectException(InvalidInetArrayItemForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return [
            'invalid type' => ['not-an-array'],
            'invalid IPv4' => [['256.256.256.256']],
            'invalid IPv6' => [['2001:xyz::1']],
            'invalid CIDR format' => [['192.168.1.0/xyz']],
            'invalid IPv4 CIDR netmask' => [['192.168.1.0/33']],
            'invalid IPv6 CIDR netmask' => [['2001:db8::/129']],
            'wrong format' => [['not-an-ip-address']],
            'incomplete IPv4' => [['192.168.1']],
            'incomplete IPv6' => [['2001:db8']],
            'mixed valid and invalid' => [['192.168.1.1', 'invalid-ip']],
            'empty string' => [['']],
            'whitespace only' => [[' ']],
            'malformed CIDR with spaces' => [['192.168.1.0 / 24']],
            'IPv6 with invalid segment count' => [['2001:db8:1:2:3:4:5:6:7']],
            'IPv6 with invalid segment length' => [['2001:db8:xyz:1:1:1:1:1']],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideInvalidDatabaseValuesForPHPTransformation
     */
    public function throws_exception_when_invalid_data_provided_to_convert_to_php_value(string $postgresValue): void
    {
        $this->expectException(InvalidInetArrayItemForPHPException::class);

        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidDatabaseValuesForPHPTransformation(): array
    {
        return [
            'invalid format' => ['{"invalid-ip"}'],
            'invalid IPv4 in array' => ['{"256.256.256.256"}'],
            'invalid IPv6 in array' => ['{"2001:xyz::1"}'],
            'malformed array' => ['not-an-array'],
            'empty item in array' => ['{"192.168.1.1",""}'],
            'invalid item in array' => ['{"192.168.1.1","invalid-ip"}'],
            'invalid CIDR in array' => ['{"192.168.1.0/33"}'],
            'incomplete IPv4 in array' => ['{"192.168.1"}'],
            'incomplete IPv6 in array' => ['{"2001:db8"}'],
        ];
    }
}
