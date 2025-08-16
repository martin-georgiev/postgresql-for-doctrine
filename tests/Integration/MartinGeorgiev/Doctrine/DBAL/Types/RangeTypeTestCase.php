<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;
use PHPUnit\Framework\Attributes\Test;

abstract class RangeTypeTestCase extends TestCase
{
    use RangeAssertionTrait;

    /**
     * Override to handle Range-specific value object comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof RangeValueObject || !$actual instanceof RangeValueObject) {
            throw new \InvalidArgumentException('assertTypeValueEquals in RangeTypeTestCase expects RangeValueObject arguments.');
        }

        $this->assertRangeEquals($expected, $actual, $typeName);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, null);
    }

    /**
     * Data-driven test for Range value objects.
     * Subclasses should add #[DataProvider('provideValidTransformations')].
     *
     * @param RangeValueObject<\DateTimeInterface|float|int> $rangeValueObject
     */
    public function can_handle_range_values(string $testName, RangeValueObject $rangeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, $rangeValueObject);
    }
}
