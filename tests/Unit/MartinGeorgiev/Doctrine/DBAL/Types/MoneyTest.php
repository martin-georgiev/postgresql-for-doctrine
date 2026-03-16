<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMoneyForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Money $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Money();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('money', $this->fixture->getName());
    }

    #[Test]
    public function converts_null_to_database_value(): void
    {
        $this->assertNull($this->fixture->convertToDatabaseValue(null, $this->platform));
    }

    #[Test]
    public function converts_null_to_php_value(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue(null, $this->platform));
    }

    #[Test]
    public function converts_empty_string_from_database_to_null(): void
    {
        $this->assertNull($this->fixture->convertToPHPValue('', $this->platform));
    }

    /**
     * @return array<string, array{moneyString: string}>
     */
    public static function provideValidMoneyStrings(): array
    {
        return [
            'USD with thousands separator' => ['moneyString' => '$1,234.56'],
            'zero value' => ['moneyString' => '$0.00'],
            'negative USD' => ['moneyString' => '-$99.99'],
            'GBP with European format' => ['moneyString' => '£1.234,56'],
        ];
    }

    #[DataProvider('provideValidMoneyStrings')]
    #[Test]
    public function round_trips_valid_money_strings_through_php(string $moneyString): void
    {
        $this->assertSame($moneyString, $this->fixture->convertToPHPValue($moneyString, $this->platform));
    }

    #[DataProvider('provideValidMoneyStrings')]
    #[Test]
    public function round_trips_valid_money_strings_to_database(string $moneyString): void
    {
        $this->assertSame($moneyString, $this->fixture->convertToDatabaseValue($moneyString, $this->platform));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'integer input' => [42],
            'float input' => [3.14],
            'array input' => [['$1.00']],
            'object input' => [new \stdClass()],
            'boolean input' => [true],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidMoneyForDatabaseException::class);

        $this->fixture->convertToPHPValue($value, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'integer input' => [42],
            'array input' => [['$1.00']],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void
    {
        $this->expectException(InvalidMoneyForPHPException::class);

        $this->fixture->convertToDatabaseValue($value, $this->platform);
    }
}
