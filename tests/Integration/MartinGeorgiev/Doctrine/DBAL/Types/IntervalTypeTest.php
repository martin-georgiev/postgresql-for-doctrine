<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class IntervalTypeTest extends TestCase
{
    use IntervalAssertionTrait;

    protected function getTypeName(): string
    {
        return 'interval';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(IntervalValueObject::class, $expected);
        $this->assertIntervalEquals($expected, $actual, $typeName);
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(IntervalValueObject $intervalValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $intervalValueObject);
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
    public function roundtrips_for_various_input_formats(\DateInterval|IntervalValueObject|string $input, string $expectedOutput): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($this->getPostgresTypeName());

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $input, $this->getTypeName())
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($this->getTypeName(), $tableName, $columnName);

            $this->assertIntervalEquals($expectedOutput, $retrieved, $this->getTypeName());
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{\DateInterval|IntervalValueObject|string, string}>
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
            'DateInterval' => [new \DateInterval('P1Y2M3D'), '1 year 2 mons 3 days'],
            'IntervalValueObject' => [IntervalValueObject::fromString('1 year 2 months 3 days'), '1 year 2 mons 3 days'],
        ];
    }

    /**
     * Each pair is a PostgreSQL interval literal and the representation the value object must
     * produce after reading it back, whatever IntervalStyle PostgreSQL wrote it in.
     *
     * @return list<array{string, string}>
     */
    private function intervalsWrittenByPostgres(): array
    {
        return [
            ['-1.5 days', '-1 day -12:00:00'],
            ['1 week', '7 days'],
            ['1 year 2 mons ago', '-1 year -2 mons'],
            ['0', '00:00:00'],
            ['3 days', '3 days'],
            ['10 mons 3 days', '10 mons 3 days'],
            ['1 year 2 mons 3 days 04:05:06', '1 year 2 mons 3 days 04:05:06'],
            ['4:05:06.5', '04:05:06.5'],
            ['1 min 30 secs', '00:01:30'],
            ['1 day -02:03:04', '1 day -02:03:04'],
            ['-5 days -04:00:00', '-5 days -04:00:00'],
            ['-00:00:01', '-00:00:01'],
        ];
    }

    #[DataProvider('provideIntervalStyles')]
    #[Test]
    public function reads_values_written_under_any_interval_style(string $intervalStyle): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($this->getPostgresTypeName());
        $fullTableName = self::DATABASE_SCHEMA.'.'.$tableName;
        $expectations = $this->intervalsWrittenByPostgres();

        try {
            // quoteStringLiteral() returns string on every supported DBAL major, while quote() is
            // declared mixed on DBAL 3 and below
            $quotedIntervalStyle = $this->connection->getDatabasePlatform()->quoteStringLiteral($intervalStyle);
            $this->connection->executeStatement(\sprintf('SET IntervalStyle = %s', $quotedIntervalStyle));

            foreach ($expectations as [$literal]) {
                $this->connection->executeStatement(
                    \sprintf('INSERT INTO %s ("%s") VALUES (CAST(? AS interval))', $fullTableName, $columnName),
                    [$literal]
                );
            }

            $storedValues = $this->connection->fetchFirstColumn(\sprintf('SELECT "%s" FROM %s ORDER BY id', $columnName, $fullTableName));
            $this->assertCount(\count($expectations), $storedValues);

            $type = Type::getType($this->getTypeName());
            $platform = $this->connection->getDatabasePlatform();

            foreach ($expectations as $index => [$literal, $expectedOutput]) {
                $converted = $type->convertToPHPValue($storedValues[$index], $platform);

                $this->assertInstanceOf(IntervalValueObject::class, $converted);
                $this->assertSame(
                    $expectedOutput,
                    (string) $converted,
                    \sprintf('Interval %s written as %s under IntervalStyle %s', $literal, \var_export($storedValues[$index], true), $intervalStyle)
                );
            }
        } finally {
            $this->connection->executeStatement('RESET IntervalStyle');
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideIntervalStyles(): array
    {
        return [
            'postgres' => ['postgres'],
            'postgres_verbose' => ['postgres_verbose'],
            'sql_standard' => ['sql_standard'],
            'iso_8601' => ['iso_8601'],
        ];
    }
}
