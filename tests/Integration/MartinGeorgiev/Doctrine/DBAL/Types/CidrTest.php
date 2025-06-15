<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class CidrTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_cidr';
    }

    protected function getColumnName(): string
    {
        return 'cidr_column';
    }

    protected function getColumnType(): string
    {
        return 'CIDR';
    }

    #[DataProvider('provideValidCidr')]
    public function test_basic_cidr_operations(?string $testData, ?string $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);

        $this->assertCount(1, $results);
        if ($expected === null) {
            $this->assertNull($results[0]);
        } else {
            $this->assertIsString($results[0]);
            $this->assertEquals($expected, $results[0]);
        }
    }

    #[DataProvider('provideInvalidCidr')]
    public function test_cidr_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidCidr(): array
    {
        return [
            'ipv4 network' => [
                '192.168.1.0/24',
                '192.168.1.0/24',
            ],
            'ipv4 network with mask' => [
                '10.0.0.0/8',
                '10.0.0.0/8',
            ],
            'ipv6 network' => [
                '2001:db8::/32',
                '2001:db8::/32',
            ],
            'ipv6 network with mask' => [
                'fe80::/10',
                'fe80::/10',
            ],
            'null value' => [
                null,
                null,
            ],
        ];
    }

    public static function provideInvalidCidr(): array
    {
        return [
            'invalid network' => [
                'invalid.network/24',
            ],
            'invalid prefix length' => [
                '192.168.1.0/33',
            ],
            'missing prefix length' => [
                '192.168.1.0',
            ],
            'invalid ipv4 format' => [
                '192.168.1/24',
            ],
            'invalid ipv6 format' => [
                '2001:db8/32',
            ],
        ];
    }
}
