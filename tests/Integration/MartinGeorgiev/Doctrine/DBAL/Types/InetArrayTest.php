<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class InetArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_inet_array';
    }

    protected function getColumnName(): string
    {
        return 'inet_array_column';
    }

    protected function getColumnType(): string
    {
        return 'INET[]';
    }

    #[DataProvider('provideValidInetArray')]
    public function test_basic_inet_array_operations(?string $testData, ?array $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);
        $this->assertSame($expected, $results);
    }

    #[DataProvider('provideInvalidInetArray')]
    public function test_inet_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidInetArray(): array
    {
        return [
            'ipv4 addresses' => ['{192.168.1.1,10.0.0.1}', ['192.168.1.1', '10.0.0.1']],
            'ipv6 addresses' => ['{2001:db8::1,fe80::1}', ['2001:db8::1', 'fe80::1']],
            'ipv4 addresses with masks' => ['{192.168.1.1/24,10.0.0.1/8}', ['192.168.1.1/24', '10.0.0.1/8']],
            'ipv6 addresses with masks' => ['{2001:db8::1/64,fe80::1/10}', ['2001:db8::1/64', 'fe80::1/10']],
            'mixed addresses' => ['{192.168.1.1,2001:db8::1}', ['192.168.1.1', '2001:db8::1']],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidInetArray(): array
    {
        return [
            'invalid values' => ['{192.168.1.1,invalid}'],
            'missing braces' => ['192.168.1.1,10.0.0.1'],
            'invalid format' => ['{192.168.1.1;10.0.0.1}'],
            'extra spaces' => ['{ 192.168.1.1 , 10.0.0.1 }'],
        ];
    }
}
