<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class IntervalTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'interval';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INTERVAL';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(IntervalValueObject::class, $expected);
        $this->assertInstanceOf(IntervalValueObject::class, $actual);
        $this->assertSame((string) $expected, (string) $actual);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(IntervalValueObject $intervalValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $intervalValueObject);
    }

    /**
     * @return array<string, array{IntervalValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'one year' => [IntervalValueObject::fromString('1 year')],
            'time only' => [IntervalValueObject::fromString('04:05:06')],
            'full' => [IntervalValueObject::fromString('1 year 2 mons 3 days 04:05:06')],
            'negative time' => [IntervalValueObject::fromString('-04:05:06')],
            'zero' => [IntervalValueObject::fromString('00:00:00')],
        ];
    }

    #[DataProvider('provideVariousInputFormats')]
    #[Test]
    public function can_round_trip_various_input_formats(string $input, string $expectedOutput): void
    {
        $inputInterval = IntervalValueObject::fromString($input);

        [$tableName, $columnName] = $this->prepareTestTable($this->getPostgresTypeName());

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $inputInterval, $this->getTypeName())
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);

            $this->assertInstanceOf(IntervalValueObject::class, $retrieved);
            $this->assertSame($expectedOutput, (string) $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideVariousInputFormats(): array
    {
        return [
            'ISO 8601 year' => ['P1Y', '1 year'],
            'ISO 8601 full' => ['P1Y2M3DT4H5M6S', '1 year 2 mons 3 days 04:05:06'],
            'verbose months' => ['1 year 2 months 3 days', '1 year 2 mons 3 days'],
            'verbose full' => ['1 year 2 months 3 days 4 hours 5 minutes 6 seconds', '1 year 2 mons 3 days 04:05:06'],
            'mixed signs: negative year positive time' => ['-1 years +04:05:06', '-1 year +04:05:06'],
            'mixed signs: negative days positive time' => ['-3 days +02:00:00', '-3 days +02:00:00'],
            'all negative' => ['-1 years -2 mons -3 days -04:05:06', '-1 year -2 mons -3 days -04:05:06'],
            'large hours' => ['100:00:00', '100:00:00'],
            'fractional seconds' => ['00:00:01.123456', '00:00:01.123456'],
            'days only' => ['30 days', '30 days'],
            'PG normalizes month overflow' => ['1 year 14 mons', '2 years 2 mons'],
        ];
    }
}
