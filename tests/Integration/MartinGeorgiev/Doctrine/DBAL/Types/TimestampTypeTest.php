<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMP;
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
        $bcEraLeapDay = \DateTimeImmutable::createFromFormat('X-m-d H:i:s.u', '+0000-02-29 10:30:45.123456');
        $expandedYear = \DateTimeImmutable::createFromFormat('X-m-d H:i:s.u', '+10000-01-15 10:30:45.123456');
        \assert($bcEraLeapDay instanceof \DateTimeImmutable);
        \assert($expandedYear instanceof \DateTimeImmutable);

        return [
            'positive infinity' => [DateTimeInfinity::POSITIVE],
            'negative infinity' => [DateTimeInfinity::NEGATIVE],
            'whole second' => [new \DateTimeImmutable('2023-06-15 10:30:45')],
            'with microseconds' => [new \DateTimeImmutable('2023-06-15 10:30:45.123456')],
            'midnight' => [new \DateTimeImmutable('2000-01-01 00:00:00')],
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
        $bcEra = \DateTimeImmutable::createFromFormat('X-m-d H:i:s.u', '+0000-01-15 10:30:45.000000');
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
            'standard timestamp' => [
                'postgresLiteral' => '2023-06-15 10:30:45',
                'expectedValue' => new \DateTimeImmutable('2023-06-15 10:30:45'),
            ],
            'first year of the BC era' => [
                'postgresLiteral' => '0001-01-15 10:30:45 BC',
                'expectedValue' => $bcEra,
            ],
        ];
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_non_datetime_value(mixed $value): void
    {
        $this->expectException(InvalidTimestampForDatabaseException::class);

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
            'string value' => ['2023-06-15 10:30:45'],
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
            $expected->format('X-m-d H:i:s.u'),
            $actual->format('X-m-d H:i:s.u'),
            \sprintf('Timestamp mismatch for type %s', $typeName)
        );
    }
}
