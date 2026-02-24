<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for EUI-64 MAC addresses (macaddr8).
 *
 * @since 4.3
 */
trait Macaddr8ValidationTrait
{
    protected function isValidMacaddr8Address(string $value): bool
    {
        // Colon-separated: 08:00:2b:ff:fe:01:02:03
        if (\preg_match('/^([0-9A-Fa-f]{2}:){7}[0-9A-Fa-f]{2}$/', $value)) {
            return true;
        }

        // Hyphen-separated: 08-00-2b-ff-fe-01-02-03
        if (\preg_match('/^([0-9A-Fa-f]{2}-){7}[0-9A-Fa-f]{2}$/', $value)) {
            return true;
        }

        // Dot notation (groups of 4): 0800.2bff.fe01.0203
        if (\preg_match('/^[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}$/', $value)) {
            return true;
        }

        // No separator: 08002bfffe010203 (16 hex chars)
        return (bool) \preg_match('/^[0-9A-Fa-f]{16}$/', $value);
    }

    protected function normalizeMacaddr8Format(string $value): string
    {
        $clean = \strtolower(\str_replace([':', '-', '.'], '', $value));

        return \implode(':', \str_split($clean, 2));
    }
}
