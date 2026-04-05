<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

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

    /**
     * @return array<string, array{TsRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple tsrange' => [new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                false,
                false
            )],
            'tsrange with inclusive bounds' => [new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 09:00:00'),
                new \DateTimeImmutable('2023-01-01 17:00:00'),
                true,
                true
            )],
            'tsrange with same day' => [new TsRangeValueObject(
                new \DateTimeImmutable('2023-06-15 08:00:00'),
                new \DateTimeImmutable('2023-06-15 16:00:00'),
                false,
                false
            )],
            'tsrange with midnight' => [new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 00:00:00'),
                new \DateTimeImmutable('2023-01-01 23:59:59'),
                false,
                false
            )],
        ];
    }

    #[DataProvider('provideInfinityAndSpecialCases')]
    #[Test]
    public function can_handle_infinity_and_special_cases(TsRangeValueObject $tsRangeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $tsRangeValueObject);
    }

    /**
     * @return array<string, array{TsRangeValueObject}>
     */
    public static function provideInfinityAndSpecialCases(): array
    {
        return [
            'unbounded range' => [new TsRangeValueObject(null, null, false, false)],
            'unbounded lower' => [new TsRangeValueObject(null, new \DateTimeImmutable('2024-12-31 23:59:59'), false, false)],
            'unbounded upper' => [new TsRangeValueObject(new \DateTimeImmutable('2024-01-01 00:00:00'), null, false, false)],
            'lower bounded infinity' => [new TsRangeValueObject(null, new \DateTimeImmutable('2024-12-31 23:59:59'), true, false, false, true, false)],
            'upper bounded infinity' => [new TsRangeValueObject(new \DateTimeImmutable('2024-01-01 00:00:00'), null, true, false, false, false, true)],
            'both bounds infinity' => [new TsRangeValueObject(null, null, true, false, false, true, true)],
            'empty range' => [new TsRangeValueObject(
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                false,
                false
            )],
        ];
    }

    /**
     * @return array<string, array{string, array<int>}> [dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'is contained by tsrange' => ['SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[2023-01-01 12:00:00,2023-01-01 16:00:00)\', r.tsRange) = TRUE', [1]],
            'overlaps tsrange' => ['SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.tsRange, \'[2023-01-01 16:00:00,2023-01-01 20:00:00)\') = TRUE', [1]],
        ];
    }
}
