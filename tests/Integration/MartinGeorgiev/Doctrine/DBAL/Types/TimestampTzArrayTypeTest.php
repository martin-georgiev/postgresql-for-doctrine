<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTzArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMPTZ_ARRAY;
    }

    /**
     * @return array<string, array{array<int, \DateTimeImmutable|DateTimeInfinity|null>}>
     */
    public static function provideValidTransformations(): array
    {
        $bcEraLeapDay = \DateTimeImmutable::createFromFormat('X-m-d H:i:s.uP', '+0000-02-29 10:30:45.123456+00:00');
        $expandedYear = \DateTimeImmutable::createFromFormat('X-m-d H:i:s.uP', '+10000-01-15 10:30:45.123456+00:00');
        \assert($bcEraLeapDay instanceof \DateTimeImmutable);
        \assert($expandedYear instanceof \DateTimeImmutable);

        return [
            'timestamptz values bounded by infinity' => [[
                DateTimeInfinity::POSITIVE,
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                DateTimeInfinity::NEGATIVE,
            ]],
            'leap day of the BC era' => [[$bcEraLeapDay]],
            'five digit year' => [[$expandedYear]],
            'single UTC timestamp' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
            ]],
            'positive timezone offset' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45+02:00'),
            ]],
            'negative timezone offset' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45-05:00'),
            ]],
            'multiple timestamptz values' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                new \DateTimeImmutable('2024-01-01 00:00:00+02:00'),
            ]],
            'timestamptz with null item' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45+00:00'),
                null,
                new \DateTimeImmutable('2024-01-01 00:00:00-05:00'),
            ]],
            'timestamptz with microseconds' => [[
                new \DateTimeImmutable('2023-06-15 10:30:45.123456+00:00'),
            ]],
            'empty timestamptz array' => [[]],
        ];
    }

    #[Test]
    public function reads_values_emitted_by_postgres(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $result = $this->fetchConvertedValueForPostgresLiteral(
            $typeName,
            $columnType,
            '{"2023-06-15 10:30:45+00",infinity,-infinity,"0001-01-15 10:30:45+00 BC","0001-02-29 10:30:45+00 BC","10000-01-15 10:30:45+00"}'
        );

        $this->assertIsArray($result);
        $this->assertSame(
            [
                '+2023-06-15 10:30:45+00:00',
                DateTimeInfinity::POSITIVE,
                DateTimeInfinity::NEGATIVE,
                '+0000-01-15 10:30:45+00:00',
                '+0000-02-29 10:30:45+00:00',
                '+10000-01-15 10:30:45+00:00',
            ],
            \array_map(
                static fn (mixed $item): mixed => $item instanceof \DateTimeImmutable ? $item->format('X-m-d H:i:sP') : $item,
                $result
            )
        );
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_non_datetime_item(mixed $value): void
    {
        $this->expectException(InvalidTimestampTzArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, [$value]);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidItems(): array
    {
        return [
            'string value' => ['2023-06-15 10:30:45+00:00'],
            'integer value' => [20230615],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('TimestampTzArray count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);
            } elseif ($expectedItem instanceof DateTimeInfinity) {
                $this->assertSame(
                    $expectedItem,
                    $actual[$index],
                    \sprintf('Infinity mismatch at index %d for type %s', $index, $typeName)
                );
            } else {
                $this->assertInstanceOf(\DateTimeImmutable::class, $expectedItem);
                $actualItem = $actual[$index];
                $this->assertInstanceOf(\DateTimeImmutable::class, $actualItem);
                $this->assertSame(
                    $expectedItem->getTimestamp(),
                    $actualItem->getTimestamp(),
                    \sprintf('TimestampTz mismatch at index %d for type %s', $index, $typeName)
                );
                $this->assertSame(
                    (int) $expectedItem->format('u'),
                    (int) $actualItem->format('u'),
                    \sprintf('TimestampTz microseconds mismatch at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
