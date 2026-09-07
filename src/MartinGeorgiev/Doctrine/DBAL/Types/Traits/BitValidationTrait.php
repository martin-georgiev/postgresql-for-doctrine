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
        // Anchored with \z rather than $, which would also match before a trailing newline.
        return \preg_match('/^[01]+\z/', $value) === 1;
    }
}
