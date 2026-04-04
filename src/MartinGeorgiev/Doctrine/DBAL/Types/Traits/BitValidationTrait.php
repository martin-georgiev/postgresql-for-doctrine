<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for bit string values.
 *
 * @since 4.5
 */
trait BitValidationTrait
{
    protected function isValidBitString(string $value): bool
    {
        return \preg_match('/^[01]+$/', $value) === 1;
    }
}
