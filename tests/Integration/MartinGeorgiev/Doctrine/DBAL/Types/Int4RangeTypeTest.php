<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class Int4RangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4range';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INT4RANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        parent::can_handle_range_values($testName, $rangeValueObject);
    }

    /**
     * @return array<string, array{string, Int4RangeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple int4range' => ['simple int4range', new Int4RangeValueObject(1, 10, true, false)],
            'int4range with inclusive bounds' => ['int4range with inclusive bounds', new Int4RangeValueObject(5, 15, true, false)],
            'int4range with negative values' => ['int4range with negative values', new Int4RangeValueObject(-100, 100, true, false)],
            'int4range with max values' => ['int4range with max values', new Int4RangeValueObject(-2147483648, 2147483647, true, false)],
        ];
    }

    #[Test]
    public function can_handle_infinite_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $int4Range = new Int4RangeValueObject(null, null, false, false);
        $this->runDbalBindingRoundTrip($typeName, $columnType, $int4Range);
    }

    #[Test]
    public function can_handle_empty_ranges(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        // lower > upper shall result in an empty range
        $int4Range = new Int4RangeValueObject(10, 5, false, false);
        $this->runDbalBindingRoundTrip($typeName, $columnType, $int4Range);
    }

    /**
     * @return array<string, array{string, string, array<int>}> [name, dql, expectedIds]
     */
    public static function provideOperatorScenarios(): array
    {
        return [
            'contains int4range' => ['contains int4range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE CONTAINS(r.int4Range, \'[3,7)\') = TRUE', [1]],
            'is contained by int4range' => ['is contained by int4range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE IS_CONTAINED_BY(\'[3,7)\', r.int4Range) = TRUE', [1]],
            'overlaps int4range' => ['overlaps int4range', 'SELECT r.id FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges r WHERE OVERLAPS(r.int4Range, \'[8,12)\') = TRUE', [1, 2]],
        ];
    }
}
