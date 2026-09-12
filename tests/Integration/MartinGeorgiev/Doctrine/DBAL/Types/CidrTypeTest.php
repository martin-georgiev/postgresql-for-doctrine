<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CidrTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'cidr';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'IPv4 CIDR' => ['192.168.1.0/24'],
            'IPv4 CIDR /8' => ['10.0.0.0/8'],
            'IPv4 CIDR /16' => ['172.16.0.0/16'],
            'IPv6 CIDR' => ['2001:db8::/32'],
            'IPv6 CIDR /64' => ['2001:db8::/64'],
            'IPv6 CIDR /128' => ['2001:db8::1/128'],
        ];
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_network(string $value): void
    {
        $this->expectException(InvalidCidrForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'invalid network format' => ['invalid-network'],
            'triple segment CIDR' => ['192.168.1.0/24/24'],
            'decimal netmask' => ['192.168.1.0/24.5'],
        ];
    }
}
