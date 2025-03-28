<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for MAC addresses.
 *
 * @since 3.0
 */
trait MacaddrValidationTrait
{
    protected function isValidMacAddress(string $value): bool
    {
        // Check if it's using colons consistently
        if (\preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $value)) {
            return true;
        }

        // Check if it's using hyphens consistently
        if (\preg_match('/^([0-9A-Fa-f]{2}-){5}[0-9A-Fa-f]{2}$/', $value)) {
            return true;
        }

        // Check for no separators
        return (bool) \preg_match('/^[0-9A-Fa-f]{12}$/', $value);
    }

    protected function normalizeFormat(string $value): string
    {
        // Remove all delimiters and convert to lowercase
        $clean = \strtolower(\str_replace([':', '-', '.'], '', $value));

        // Insert colons every 2 characters
        return \implode(':', \str_split($clean, 2));
    }
}
