<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TstzRangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tstzrange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSTZRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, TstzRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple tstzrange' => ['simple tstzrange', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                false,
                false
            )],
            'tstzrange with inclusive bounds' => ['tstzrange with inclusive bounds', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 09:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 17:00:00+00:00'),
                true,
                true
            )],
            'tstzrange with UTC' => ['tstzrange with UTC', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-06-15 08:00:00+00:00'),
                new \DateTimeImmutable('2023-06-15 16:00:00+00:00'),
                false,
                false
            )],
        ];
    }

    #[Test]
    #[DataProvider('provideInfinityAndSpecialCases')]
    public function can_handle_infinity_and_special_cases(string $testName, TstzRangeValueObject $tstzRangeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $tstzRangeValueObject);
    }

    /**
     * @return array<string, array{string, TstzRangeValueObject}>
     */
    public static function provideInfinityAndSpecialCases(): array
    {
        return [
            'unbounded range' => ['unbounded range', new TstzRangeValueObject(null, null, false, false)],
            'unbounded lower' => ['unbounded lower', new TstzRangeValueObject(null, new \DateTimeImmutable('2024-12-31 23:59:59+00:00'), true, false)],
            'unbounded upper' => ['unbounded upper', new TstzRangeValueObject(new \DateTimeImmutable('2024-01-01 00:00:00+00:00'), null, true, false)],
            'lower bounded infinity' => ['lower bounded infinity', new TstzRangeValueObject(null, new \DateTimeImmutable('2024-12-31 23:59:59+00:00'), true, false, false, true, false)],
            'upper bounded infinity' => ['upper bounded infinity', new TstzRangeValueObject(new \DateTimeImmutable('2024-01-01 00:00:00+00:00'), null, true, false, false, false, true)],
            'both bounds infinity' => ['both bounds infinity', new TstzRangeValueObject(null, null, true, false, false, true, true)],
            'empty range' => ['empty range', new TstzRangeValueObject(
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                false,
                false
            )],
        ];
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'is contained by tstzrange' => ['is contained by tstzrange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[2023-01-01 12:00:00+00,2023-01-01 16:00:00+00)\', r.tstzRange) = TRUE', [1]],
            'overlaps tstzrange' => ['overlaps tstzrange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.tstzRange, \'[2023-01-01 16:00:00+00,2023-01-01 20:00:00+00)\') = TRUE', [1]],
        ];
    }
}
