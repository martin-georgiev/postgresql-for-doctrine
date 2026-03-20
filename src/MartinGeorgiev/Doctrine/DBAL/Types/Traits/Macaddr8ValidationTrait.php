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
    protected function isValidMacAddress(string $value): bool
    {
        if ($this->isValid8ByteMacAddress($value)) {
            return true;
        }

        return (bool) $this->isValid6ByteMacAddress($value);
    }

    private function isValid8ByteMacAddress(string $value): bool
    {
        return (bool) \preg_match(
            '/^(?:'
            .'[0-9A-Fa-f]{2}(?::[0-9A-Fa-f]{2}){7}'
            .'|[0-9A-Fa-f]{2}(?:-[0-9A-Fa-f]{2}){7}'
            .'|[0-9A-Fa-f]{6}:[0-9A-Fa-f]{10}'
            .'|[0-9A-Fa-f]{6}-[0-9A-Fa-f]{10}'
            .'|[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}'
            .'|[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}'
            .'|[0-9A-Fa-f]{8}:[0-9A-Fa-f]{8}'
            .'|[0-9A-Fa-f]{16}'
            .')$/',
            $value
        );
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
            .')$/',
            $value
        );
    }

    protected function normalize8ByteMacFormat(string $value): string
    {
        $clean = \strtolower(\str_replace([':', '-', '.'], '', $value));

        return \implode(':', \str_split($clean, 2));
    }
}
