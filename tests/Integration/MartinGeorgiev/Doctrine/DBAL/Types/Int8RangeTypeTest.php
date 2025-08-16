<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class Int8RangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int8range';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INT8RANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, Int8RangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple int8range' => ['simple int8range', new Int8RangeValueObject(1, 1000, true, false)],
            'int8range with inclusive bounds' => ['int8range with inclusive bounds', new Int8RangeValueObject(5, 16, true, false)],
            'int8range with negative values' => ['int8range with negative values', new Int8RangeValueObject(-999999, 1000000, true, false)],
            'int8range with max values' => ['int8range with max values', new Int8RangeValueObject(PHP_INT_MIN + 1, PHP_INT_MAX, true, false)],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $int8Range = new Int8RangeValueObject(null, null, false, false);
        $this->runTypeTest($typeName, $columnType, $int8Range);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $int8Range = new Int8RangeValueObject(10, 5, false, false);
        $this->runTypeTest($typeName, $columnType, $int8Range);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'contains int8range' => ['contains int8range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE CONTAINS(r.int8Range, \'[150,800)\') = TRUE', [1]],
            'is contained by int8range' => ['is contained by int8range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[200,800)\', r.int8Range) = TRUE', [1]],
            'overlaps int8range' => ['overlaps int8range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.int8Range, \'[800,1200)\') = TRUE', [1, 2]],
        ];
    }
}
