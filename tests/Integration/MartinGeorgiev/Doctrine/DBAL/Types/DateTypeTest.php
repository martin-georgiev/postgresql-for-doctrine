<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::DATE;
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(\DateTimeImmutable|DateTimeInfinity $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{\DateTimeImmutable|DateTimeInfinity}>
     */
    public static function provideValidTransformations(): array
    {
        $bcEraLeapDay = \DateTimeImmutable::createFromFormat('X-m-d H:i:s', '+0000-02-29 00:00:00');
        $expandedYear = \DateTimeImmutable::createFromFormat('X-m-d H:i:s', '+10000-01-15 00:00:00');
        \assert($bcEraLeapDay instanceof \DateTimeImmutable);
        \assert($expandedYear instanceof \DateTimeImmutable);

        return [
            'positive infinity' => [DateTimeInfinity::POSITIVE],
            'negative infinity' => [DateTimeInfinity::NEGATIVE],
            'single date' => [new \DateTimeImmutable('2023-06-15')],
            'leap day' => [new \DateTimeImmutable('2024-02-29')],
            'date with time component stripped' => [new \DateTimeImmutable('2023-06-15 15:30:45')],
            'leap day of the BC era' => [$bcEraLeapDay],
            'five digit year' => [$expandedYear],
        ];
    }

    #[DataProvider('provideValuesEmittedByPostgres')]
    #[Test]
    public function reads_values_emitted_by_postgres(string $postgresLiteral, \DateTimeImmutable|DateTimeInfinity $expectedValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $result = $this->fetchConvertedValueForPostgresLiteral($typeName, $columnType, $postgresLiteral);

        $this->assertTypeValueEquals($expectedValue, $result, $typeName);
    }

    /**
     * @return array<string, array{postgresLiteral: string, expectedValue: \DateTimeImmutable|DateTimeInfinity}>
     */
    public static function provideValuesEmittedByPostgres(): array
    {
        $bcEra = \DateTimeImmutable::createFromFormat('X-m-d H:i:s', '+0000-01-15 00:00:00');
        \assert($bcEra instanceof \DateTimeImmutable);

        return [
            'positive infinity' => [
                'postgresLiteral' => 'infinity',
                'expectedValue' => DateTimeInfinity::POSITIVE,
            ],
            'negative infinity' => [
                'postgresLiteral' => '-infinity',
                'expectedValue' => DateTimeInfinity::NEGATIVE,
            ],
            'standard date' => [
                'postgresLiteral' => '2023-06-15',
                'expectedValue' => new \DateTimeImmutable('2023-06-15'),
            ],
            'first year of the BC era' => [
                'postgresLiteral' => '0001-01-15 BC',
                'expectedValue' => $bcEra,
            ],
        ];
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_non_datetime_value(mixed $value): void
    {
        $this->expectException(InvalidDateForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'string value' => ['2023-06-15'],
            'integer value' => [20230615],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if ($expected instanceof DateTimeInfinity) {
            $this->assertSame($expected, $actual, \sprintf('Infinity mismatch for type %s', $typeName));

            return;
        }

        $this->assertInstanceOf(\DateTimeImmutable::class, $expected);
        $this->assertInstanceOf(\DateTimeImmutable::class, $actual);
        $this->assertSame(
            $expected->format('X-m-d'),
            $actual->format('X-m-d'),
            \sprintf('Date mismatch for type %s', $typeName)
        );
        $this->assertSame(
            '00:00:00',
            $actual->format('H:i:s'),
            \sprintf('Time component must be zeroed for type %s', $typeName)
        );
    }
}
