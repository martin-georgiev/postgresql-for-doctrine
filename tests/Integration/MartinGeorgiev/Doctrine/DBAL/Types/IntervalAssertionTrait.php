<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval as IntervalValueObject;

/**
 * Shared assertion logic for Interval-related tests.
 *
 * Provides string-representation comparison utilities to eliminate code duplication
 * between Interval test classes.
 */
trait IntervalAssertionTrait
{
    protected function assertIntervalEquals(IntervalValueObject|string $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(
            IntervalValueObject::class,
            $actual,
            'Failed asserting that value is an IntervalValueObject for type '.$typeName
        );
        $expectedObj = \is_string($expected) ? IntervalValueObject::fromString($expected) : $expected;
        $this->assertSame(
            (string) $expectedObj,
            (string) $actual,
            'Failed asserting that interval string representations are identical for type '.$typeName
        );
    }
}
