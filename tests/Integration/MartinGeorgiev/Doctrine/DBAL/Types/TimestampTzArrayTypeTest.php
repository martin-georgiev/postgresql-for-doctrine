<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTimestampTzArrayItemForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TimestampTzArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::TIMESTAMPTZ_ARRAY;
    }

    /**
     * @return array<string, array{array<int, \DateTimeImmutable|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
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
