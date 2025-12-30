<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CidrArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'cidr[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'CIDR[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple cidr array' => ['simple cidr array', ['192.168.1.0/24', '10.0.0.0/8']],
            'cidr array with IPv6' => ['cidr array with IPv6', ['2001:db8::/32', '2001:db8::/64']],
            'cidr array with mixed networks' => ['cidr array with mixed networks', [
                '192.168.1.0/24',
                '172.16.0.0/16',
                '10.0.0.0/8',
                '2001:db8::/32',
            ]],
            'cidr array with single hosts' => ['cidr array with single hosts', [
                '192.168.1.1/32',
                '10.0.0.1/32',
                '2001:db8::1/128',
            ]],
            'empty cidr array' => ['empty cidr array', []],
            'cidr array with null item' => ['cidr array with null item', ['192.168.0.0/24', null, '10.0.0.0/8']],
        ];
    }

    #[Test]
    public function can_handle_invalid_networks(): void
    {
        $this->expectException(InvalidCidrArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['invalid-network', '192.168.1.0/24']);
    }
}
