<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumRangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class NumRangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'numrange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'NUMRANGE';
    }

    /**
     * @return array<string, array{NumRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple numrange' => [new NumRangeValueObject(1.5, 10.7, false, false)],
            'numrange with inclusive bounds' => [new NumRangeValueObject(5.5, 15.7, true, true)],
            'numrange with negative values' => [new NumRangeValueObject(-100.5, 100.7, false, false)],
            'numrange with high precision' => [new NumRangeValueObject(1.123456789, 10.987654321, false, false)],
        ];
    }

    #[Test]
    #[DataProvider('provideInfinityAndSpecialCases')]
    public function can_handle_infinity_and_special_cases(NumRangeValueObject $numRangeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $numRangeValueObject);
    }

    /**
     * @return array<string, array{NumRangeValueObject}>
     */
    public static function provideInfinityAndSpecialCases(): array
    {
        return [
            'unbounded range' => [new NumRangeValueObject(null, null, false, false)],
            'lower bounded infinity' => [new NumRangeValueObject(null, 100, true, false, false, true, false)],
            'upper bounded infinity' => [new NumRangeValueObject(0, null, true, false, false, false, true)],
            'both bounds infinity' => [new NumRangeValueObject(null, null, true, false, false, true, true)],
            'php inf constant upper' => [new NumRangeValueObject(0, INF)],
            'php inf constant lower' => [new NumRangeValueObject(-INF, 100)],
            'php inf constant both' => [new NumRangeValueObject(-INF, INF)],
            'empty range' => [new NumRangeValueObject(10.5, 5.7, false, false)],
        ];
    }

    /**
     * @return array<string, array{string, array<int>}> [dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'contains numrange' => ['SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE CONTAINS(r.numRange, \'[2.5,8.5)\') = TRUE', [1]],
        ];
    }
}
