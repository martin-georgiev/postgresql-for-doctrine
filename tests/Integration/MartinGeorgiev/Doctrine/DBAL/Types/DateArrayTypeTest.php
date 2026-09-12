<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return Type::DATE_ARRAY;
    }

    /**
     * @return array<string, array{array<int, \DateTimeImmutable|DateTimeInfinity|null>}>
     */
    public static function provideValidTransformations(): array
    {
        $bcEraLeapDay = \DateTimeImmutable::createFromFormat('X-m-d H:i:s', '+0000-02-29 00:00:00');
        $expandedYear = \DateTimeImmutable::createFromFormat('X-m-d H:i:s', '+10000-01-15 00:00:00');
        \assert($bcEraLeapDay instanceof \DateTimeImmutable);
        \assert($expandedYear instanceof \DateTimeImmutable);

        return [
            'dates bounded by infinity' => [[
                DateTimeInfinity::POSITIVE,
                new \DateTimeImmutable('2023-06-15'),
                DateTimeInfinity::NEGATIVE,
            ]],
            'leap day of the BC era' => [[$bcEraLeapDay]],
            'five digit year' => [[$expandedYear]],
            'single date' => [[
                new \DateTimeImmutable('2023-06-15'),
            ]],
            'multiple dates' => [[
                new \DateTimeImmutable('2023-06-15'),
                new \DateTimeImmutable('2024-02-29'),
                new \DateTimeImmutable('2000-01-01'),
            ]],
            'date with time component stripped' => [[
                new \DateTimeImmutable('2023-06-15 15:30:45'),
            ]],
            'date with null item' => [[
                new \DateTimeImmutable('2023-06-15'),
                null,
                new \DateTimeImmutable('2024-02-29'),
            ]],
            'empty date array' => [[]],
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
            '{2023-06-15,infinity,-infinity,"0001-01-15 BC","0001-02-29 BC",10000-01-15}'
        );

        $this->assertIsArray($result);
        $this->assertSame(
            [
                '+2023-06-15',
                DateTimeInfinity::POSITIVE,
                DateTimeInfinity::NEGATIVE,
                '+0000-01-15',
                '+0000-02-29',
                '+10000-01-15',
            ],
            \array_map(
                static fn (mixed $item): mixed => $item instanceof \DateTimeImmutable ? $item->format('X-m-d') : $item,
                $result
            )
        );
    }

    #[DataProvider('provideInvalidItems')]
    #[Test]
    public function rejects_non_datetime_item(mixed $value): void
    {
        $this->expectException(InvalidDateArrayItemForDatabaseException::class);

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
            'string value' => ['2023-06-15'],
            'integer value' => [20230615],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('DateArray count mismatch for type %s', $typeName));

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
                    $expectedItem->format('Y-m-d'),
                    $actualItem->format('Y-m-d'),
                    \sprintf('Date mismatch at index %d for type %s', $index, $typeName)
                );
                $this->assertSame(
                    '00:00:00',
                    $actualItem->format('H:i:s'),
                    \sprintf('Time component must be zeroed at index %d for type %s', $index, $typeName)
                );
            }
        }
    }
}
