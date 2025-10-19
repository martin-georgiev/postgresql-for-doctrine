<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TsRangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsrange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, TsRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple tsrange' => ['simple tsrange', new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                false,
                false
            )],
            'tsrange with inclusive bounds' => ['tsrange with inclusive bounds', new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 09:00:00'),
                new \DateTimeImmutable('2023-01-01 17:00:00'),
                true,
                true
            )],
            'tsrange with same day' => ['tsrange with same day', new TsRangeValueObject(
                new \DateTimeImmutable('2023-06-15 08:00:00'),
                new \DateTimeImmutable('2023-06-15 16:00:00'),
                false,
                false
            )],
            'tsrange with midnight' => ['tsrange with midnight', new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 00:00:00'),
                new \DateTimeImmutable('2023-01-01 23:59:59'),
                false,
                false
            )],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $tsRange = new TsRangeValueObject(null, null, false, false);
        $this->runDbalBindingRoundTrip($typeName, $columnType, $tsRange);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $tsRange = new TsRangeValueObject(
            new \DateTimeImmutable('2023-01-01 18:00:00'),
            new \DateTimeImmutable('2023-01-01 10:00:00'),
            false,
            false
        );
        $this->runDbalBindingRoundTrip($typeName, $columnType, $tsRange);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'is contained by tsrange' => ['is contained by tsrange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[2023-01-01 12:00:00,2023-01-01 16:00:00)\', r.tsRange) = TRUE', [1]],
            'overlaps tsrange' => ['overlaps tsrange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.tsRange, \'[2023-01-01 16:00:00,2023-01-01 20:00:00)\') = TRUE', [1]],
        ];
    }
}
