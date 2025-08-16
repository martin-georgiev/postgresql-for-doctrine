<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class MacaddrTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'macaddr';
    }

    protected function getPostgresTypeName(): string
    {
        return 'MACADDR';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'standard MAC address' => ['08:00:2b:01:02:03'],
            'MAC with zeros' => ['00:00:00:00:00:00'],
            'MAC with FF' => ['ff:ff:ff:ff:ff:ff'],
            'mixed case MAC' => ['08:00:2b:01:02:03'],
            'MAC with single digits' => ['01:02:03:04:05:06'],
        ];
    }

    #[Test]
    public function can_handle_invalid_addresses(): void
    {
        $this->expectException(InvalidMacaddrForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, 'invalid-mac');
    }
}
