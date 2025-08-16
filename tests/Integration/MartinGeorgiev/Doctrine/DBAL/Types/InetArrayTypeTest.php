<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class InetArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'inet[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INET[]';
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
            'simple inet array' => ['simple inet array', ['192.168.1.1', '10.0.0.1']],
            'inet array with IPv6' => ['inet array with IPv6', ['2001:db8::1', '::1']],
            'inet array with mixed addresses' => ['inet array with mixed addresses', [
                '192.168.1.1',
                '172.16.0.1',
                '10.0.0.1',
                '2001:db8::1',
            ]],
            'inet array with localhost' => ['inet array with localhost', [
                '127.0.0.1',
                '::1',
            ]],
            'empty inet array' => ['empty inet array', []],
        ];
    }

    #[Test]
    public function can_handle_invalid_addresses(): void
    {
        $this->expectException(InvalidInetArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, ['invalid-address', '192.168.1.1']);
    }
}
