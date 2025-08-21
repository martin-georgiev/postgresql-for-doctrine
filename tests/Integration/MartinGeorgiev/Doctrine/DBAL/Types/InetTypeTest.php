<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class InetTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'inet';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INET';
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
            'IPv4 address' => ['192.168.1.1'],
            'IPv4 with CIDR' => ['192.168.1.0/24'],
            'IPv6 address' => ['2001:db8::1'],
            'IPv6 with CIDR' => ['2001:db8::/32'],
            'localhost IPv4' => ['127.0.0.1'],
            'localhost IPv6' => ['::1'],
        ];
    }

    #[Test]
    public function can_handle_invalid_addresses(): void
    {
        $this->expectException(InvalidInetForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'invalid-address');
    }

    #[Test]
    public function can_handle_empty_string(): void
    {
        $this->expectException(InvalidInetForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '');
    }
}
