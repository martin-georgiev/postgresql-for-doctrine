<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for 8-byte/EUI-64 MAC addresses (macaddr8).
 *
 * @since 4.3
 */
trait Macaddr8ValidationTrait
{
    use MacaddrValidationTrait;

    protected function isValidMacAddress(string $value): bool
    {
        if ($this->isValid8ByteMacAddress($value)) {
            return true;
        }

        return $this->isValid6ByteMacAddress($value);
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
}
