<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CidrTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'cidr';
    }

    protected function getPostgresTypeName(): string
    {
        return 'CIDR';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
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

    #[Test]
    public function can_handle_invalid_networks(): void
    {
        $this->expectException(InvalidCidrForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'invalid-network');
    }
}
