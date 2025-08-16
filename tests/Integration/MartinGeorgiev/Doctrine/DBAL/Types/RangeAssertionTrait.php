<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

/**
 * Shared assertion logic for Range-related tests.
 *
 * Provides Range value object comparison logic including string representation
 * and empty state validation to eliminate code duplication between Range test classes.
 */
trait RangeAssertionTrait
{
    /**
     * Assert that two range value objects are equal.
     *
     * @param RangeValueObject<\DateTimeInterface|float|int> $expected
     * @param RangeValueObject<\DateTimeInterface|float|int> $actual
     */
    protected function assertRangeEquals(RangeValueObject $expected, RangeValueObject $actual, string $typeName): void
    {
        $this->assertEquals(
            $expected->__toString(),
            $actual->__toString(),
            'Range string representation mismatch for type '.$typeName
        );

        $this->assertEquals(
            $expected->isEmpty(),
            $actual->isEmpty(),
            'Range empty state mismatch for type '.$typeName
        );
    }
}
