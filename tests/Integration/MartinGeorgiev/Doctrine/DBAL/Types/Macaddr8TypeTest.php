<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class Macaddr8TypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'macaddr8';
    }

    protected function getPostgresTypeName(): string
    {
        return 'MACADDR8';
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
            'standard EUI-64' => ['08:00:2b:ff:fe:01:02:03'],
            'all zeros' => ['00:00:00:00:00:00:00:00'],
            'all ff' => ['ff:ff:ff:ff:ff:ff:ff:ff'],
            'mixed case' => ['08:00:2B:FF:FE:01:02:03'],
        ];
    }

    #[Test]
    public function can_handle_invalid_addresses(): void
    {
        $this->expectException(InvalidMacaddr8ForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'invalid-mac8');
    }
}
