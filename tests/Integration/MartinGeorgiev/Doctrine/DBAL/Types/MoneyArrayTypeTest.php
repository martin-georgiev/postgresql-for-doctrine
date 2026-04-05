<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\Test;

class MoneyArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'money[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'MONEY[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single money value' => [['$0.00']],
            'multiple money values' => [['$1.00', '$2.50', '$100.00']],
            'negative value' => [['-$99.99']],
            'large value with thousands separator' => [['$1,234.56']],
            'empty money array' => [[]],
            'money array with null item' => [['$1.00', null, '$2.00']],
        ];
    }

    #[Test]
    public function rejects_invalid_money_format(): void
    {
        $this->expectException(InvalidMoneyArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['no-digit-here']);
    }
}
