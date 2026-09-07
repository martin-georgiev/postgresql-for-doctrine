<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for 6-byte MAC addresses.
 *
 * @since 3.0
 */
trait MacaddrValidationTrait
{
    protected function isValidMacAddress(string $value): bool
    {
        return $this->isValid6ByteMacAddress($value);
    }

    private function isValid6ByteMacAddress(string $value): bool
    {
        return (bool) \preg_match(
            '/^(?:'
            .'[0-9A-Fa-f]{2}(?::[0-9A-Fa-f]{2}){5}'
            .'|[0-9A-Fa-f]{2}(?:-[0-9A-Fa-f]{2}){5}'
            .'|[0-9A-Fa-f]{6}:[0-9A-Fa-f]{6}'
            .'|[0-9A-Fa-f]{6}-[0-9A-Fa-f]{6}'
            .'|[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}'
            .'|[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}'
            .'|[0-9A-Fa-f]{12}'
            // Anchored with \z rather than $, which would also match before a trailing newline. Normalisation strips
            // separators and re-splits in pairs, so a newline would produce a malformed extra group.
            .')\z/',
            $value
        );
    }

    protected function normalizeMacAddressFormat(string $value): string
    {
        $clean = \strtolower(\str_replace([':', '-', '.'], '', $value));

        return \implode(':', \str_split($clean, 2));
    }
}
