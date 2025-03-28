<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for INET addresses.
 *
 * @since 3.0
 */
trait InetValidationTrait
{
    use NetworkAddressValidationTrait;

    protected function isValidInetAddress(string $value): bool
    {
        // Validate plain IP addresses
        if ($this->isValidPlainIpAddress($value)) {
            return true;
        }

        // Validate CIDR notation
        return $this->isValidNetworkAddressWithCidr($value);
    }
}
