<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class InetTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'inet';
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
            'IPv4 address' => ['192.168.1.1'],
            'IPv4 with CIDR' => ['192.168.1.0/24'],
            'IPv6 address' => ['2001:db8::1'],
            'IPv6 with CIDR' => ['2001:db8::/32'],
            'localhost IPv4' => ['127.0.0.1'],
            'localhost IPv6' => ['::1'],
        ];
    }

    /**
     * PostgreSQL's inet output suppresses a "trivial" all-ones netmask (/32 for IPv4, /128 for IPv6).
     * These are accepted on input but not echoed back, unlike a non-trivial netmask.
     */
    #[Test]
    public function roundtrips_ipv6_with_full_length_cidr_without_the_trivial_netmask(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, '::1/128', '::1');
    }

    #[Test]
    public function roundtrips_ipv4_mapped_ipv6_with_full_length_cidr_without_the_trivial_netmask(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, '::ffff:192.168.1.1/128', '::ffff:192.168.1.1');
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(string $value): void
    {
        $this->expectException(InvalidInetForPHPException::class);

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
            'invalid address format' => ['invalid-address'],
            'empty string' => [''],
            'triple segment CIDR' => ['1.2.3.4/24/24'],
            'decimal netmask' => ['1.2.3.4/24.5'],
        ];
    }
}
