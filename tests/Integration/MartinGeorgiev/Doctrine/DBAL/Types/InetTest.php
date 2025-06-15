<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class InetTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_inet';
    }

    protected function getColumnName(): string
    {
        return 'inet_column';
    }

    protected function getColumnType(): string
    {
        return 'INET';
    }

    #[DataProvider('provideValidInet')]
    public function test_basic_inet_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidInet')]
    public function test_inet_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidInet(): array
    {
        return [
            'ipv4 address' => [
                '192.168.1.1',
                '192.168.1.1',
            ],
            'ipv4 address with mask' => [
                '192.168.1.1/24',
                '192.168.1.1/24',
            ],
            'ipv6 address' => [
                '2001:db8::1',
                '2001:db8::1',
            ],
            'ipv6 address with mask' => [
                '2001:db8::1/64',
                '2001:db8::1/64',
            ],
            'null value' => [
                null,
                null,
            ],
        ];
    }

    public static function provideInvalidInet(): array
    {
        return [
            'invalid address' => [
                'invalid.address',
            ],
            'invalid prefix length' => [
                '192.168.1.1/33',
            ],
            'invalid ipv4 format' => [
                '192.168.1',
            ],
            'invalid ipv6 format' => [
                '2001:db8',
            ],
            'invalid characters' => [
                '192.168.1.1/abc',
            ],
        ];
    }
}
