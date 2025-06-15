<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class CidrArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_cidr_array';
    }

    protected function getColumnName(): string
    {
        return 'cidr_array_column';
    }

    protected function getColumnType(): string
    {
        return 'CIDR[]';
    }

    #[DataProvider('provideValidCidrArray')]
    public function test_basic_cidr_array_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidCidrArray')]
    public function test_cidr_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidCidrArray(): array
    {
        return [
            'ipv4 networks' => ['{192.168.1.0/24,10.0.0.0/8}', '{192.168.1.0/24,10.0.0.0/8}'],
            'ipv6 networks' => ['{2001:db8::/32,fe80::/10}', '{2001:db8::/32,fe80::/10}'],
            'ipv4-mapped ipv6' => ['{::ffff:192.168.1.0/120}', '{::ffff:192.168.1.0/120}'],
            'mixed networks' => ['{192.168.1.0/24,2001:db8::/32}', '{192.168.1.0/24,2001:db8::/32}'],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidCidrArray(): array
    {
        return [
            'invalid values' => ['{192.168.1.0/24,invalid}'],
            'missing braces' => ['192.168.1.0/24,10.0.0.0/8'],
            'invalid format' => ['{192.168.1.0/24;10.0.0.0/8}'],
            'extra spaces' => ['{ 192.168.1.0/24 , 10.0.0.0/8 }'],
        ];
    }
}
