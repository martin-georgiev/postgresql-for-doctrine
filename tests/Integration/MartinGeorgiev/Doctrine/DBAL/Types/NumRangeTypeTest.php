<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumRangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
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

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, NumRangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple numrange' => ['simple numrange', new NumRangeValueObject(1.5, 10.7, false, false)],
            'numrange with inclusive bounds' => ['numrange with inclusive bounds', new NumRangeValueObject(5.5, 15.7, true, true)],
            'numrange with negative values' => ['numrange with negative values', new NumRangeValueObject(-100.5, 100.7, false, false)],
            'numrange with high precision' => ['numrange with high precision', new NumRangeValueObject(1.123456789, 10.987654321, false, false)],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $numericRange = new NumRangeValueObject(null, null, false, false);
        $this->runDbalBindingRoundTrip($typeName, $columnType, $numericRange);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $numericRange = new NumRangeValueObject(10.5, 5.7, false, false);
        $this->runDbalBindingRoundTrip($typeName, $columnType, $numericRange);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'contains numrange' => ['contains numrange', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE CONTAINS(r.numRange, \'[2.5,8.5)\') = TRUE', [1]],
        ];
    }
}
