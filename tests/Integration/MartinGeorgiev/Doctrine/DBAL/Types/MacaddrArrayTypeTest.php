<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class MacaddrArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'macaddr[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'MACADDR[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    /**
     * @return array<string, array{string, array<int, string>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple macaddr array' => ['simple macaddr array', ['08:00:2b:01:02:03', '00:0c:29:aa:bb:cc']],
            'macaddr array with zeros' => ['macaddr array with zeros', ['00:00:00:00:00:00', 'ff:ff:ff:ff:ff:ff']],
            'macaddr array with mixed case' => ['macaddr array with mixed case', [
                '08:00:2b:01:02:03',
                '00:0c:29:aa:bb:cc',
            ]],
            'macaddr array with single digits' => ['macaddr array with single digits', [
                '01:02:03:04:05:06',
                '0a:0b:0c:0d:0e:0f',
            ]],
            'empty macaddr array' => ['empty macaddr array', []],
        ];
    }

    #[Test]
    public function can_handle_invalid_addresses(): void
    {
        $this->expectException(InvalidMacaddrArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, ['invalid-mac', '08:00:2b:01:02:03']);
    }
}
