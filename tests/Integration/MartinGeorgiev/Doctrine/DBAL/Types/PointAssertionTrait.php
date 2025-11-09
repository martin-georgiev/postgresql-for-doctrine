<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;

/**
 * Shared assertion logic for Point-related tests.
 *
 * Provides coordinate precision comparison and Point array detection
 * to eliminate code duplication between Point test classes.
 */
trait PointAssertionTrait
{
    /**
     * Assert that two point value objects are equal with coordinate precision.
     */
    protected function assertPointEquals(PointValueObject $expected, PointValueObject $actual, string $typeName): void
    {
        $this->assertEqualsWithDelta(
            $expected->getX(),
            $actual->getX(),
            0.000001,
            'X coordinate mismatch for type '.$typeName
        );

        $this->assertEqualsWithDelta(
            $expected->getY(),
            $actual->getY(),
            0.000001,
            'Y coordinate mismatch for type '.$typeName
        );
    }

    protected function isPointArray(array $array): bool
    {
        foreach ($array as $item) {
            if (!$item instanceof PointValueObject) {
                return false;
            }
        }

        return true;
    }
}
