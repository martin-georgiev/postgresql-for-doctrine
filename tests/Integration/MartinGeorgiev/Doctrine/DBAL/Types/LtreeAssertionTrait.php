<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

/**
 * Shared assertion logic for Ltree-related tests.
 *
 * Provides path comparison utilities to eliminate code duplication
 * between Ltree test classes.
 */
trait LtreeAssertionTrait
{
    protected function assertLtreeEquals(LtreeValueObject|string $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(
            LtreeValueObject::class,
            $actual,
            'Failed asserting that value is a Ltree object for type '.$typeName
        );
        $this->assertSame(
            (string) $expected,
            (string) $actual,
            'Failed asserting that ltree string representations are identical for type '.$typeName
        );
    }
}
