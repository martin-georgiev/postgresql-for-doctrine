<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateRangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'daterange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'DATERANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, DateRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple daterange' => ['simple daterange', new DateRangeValueObject(
                new \DateTimeImmutable('2023-01-02'),
                new \DateTimeImmutable('2023-12-31'),
                true,
                false
            )],
            'daterange with inclusive bounds' => ['daterange with inclusive bounds', new DateRangeValueObject(
                new \DateTimeImmutable('2023-01-01'),
                new \DateTimeImmutable('2024-01-01'),
                true,
                false
            )],
            'daterange with single day' => ['daterange with single day', new DateRangeValueObject(
                new \DateTimeImmutable('2023-06-15'),
                new \DateTimeImmutable('2023-06-16'),
                true,
                false
            )],
            'daterange with leap year' => ['daterange with leap year', new DateRangeValueObject(
                new \DateTimeImmutable('2024-02-02'),
                new \DateTimeImmutable('2024-02-29'),
                true,
                false
            )],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $dateRange = new DateRangeValueObject(null, null, false, false);
        $this->runTypeTest($typeName, $columnType, $dateRange);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $dateRange = new DateRangeValueObject(
            new \DateTimeImmutable('2023-12-31'),
            new \DateTimeImmutable('2023-01-01'),
            false,
            false
        );
        $this->runTypeTest($typeName, $columnType, $dateRange);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'contains daterange' => ['contains daterange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE CONTAINS(r.dateRange, \'[2023-02-01,2023-11-01)\') = TRUE', [1]],
            'is contained by daterange' => ['is contained by daterange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[2023-02-01,2023-11-01)\', r.dateRange) = TRUE', [1]],
            'overlaps daterange' => ['overlaps daterange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.dateRange, \'[2023-11-15,2024-01-15)\') = TRUE', [1, 2, 3]],
        ];
    }
}
