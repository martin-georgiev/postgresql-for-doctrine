<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Adds length-aware SQL declaration for types that support a dimension/length parameter.
 * Generates `TYPE(n)` when `fieldDeclaration['length']` is a positive integer, otherwise bare `TYPE`.
 */
trait LengthAwareSQLDeclarationTrait
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $length = $fieldDeclaration['length'] ?? null;

        if (\is_int($length) && $length > 0) {
            return \sprintf('%s(%d)', \strtoupper(static::TYPE_NAME), $length);
        }

        return \strtoupper(static::TYPE_NAME);
    }
}
