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
        ];
    }
}
