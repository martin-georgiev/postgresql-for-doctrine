<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class MoneyTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'money';
    }

    protected function getPostgresTypeName(): string
    {
        return 'MONEY';
    }

    #[DataProvider('provideValidRoundTrips')]
    #[Test]
    public function can_transform_from_php_value(string $testValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidRoundTrips(): array
    {
        return [
            'zero' => ['$0.00'],
            'positive with cents' => ['$1,234.56'],
            'negative' => ['-$99.99'],
        ];
    }

    #[DataProvider('provideNormalizedTransformations')]
    #[Test]
    public function can_handle_postgresql_normalization_on_storage(string $inputValue, string $expectedValue): void
    {
        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $this->getTypeName(),
            $this->getPostgresTypeName(),
            $inputValue,
            $expectedValue
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideNormalizedTransformations(): array
    {
        return [
            'plain numeric gets locale formatting' => ['1234.56', '$1,234.56'],
            'integer gets decimal places' => ['100', '$100.00'],
        ];
    }

    #[Test]
    public function rejects_invalid_money_format_before_database_write(): void
    {
        $this->expectException(InvalidMoneyForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), 'not money');
    }
}
