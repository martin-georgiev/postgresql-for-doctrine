<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for CIDR addresses.
 *
 * @since 3.0
 */
trait CidrValidationTrait
{
    use NetworkAddressValidationTrait;

    protected function isValidCidrAddress(string $value): bool
    {
        return $this->isValidNetworkAddressWithCidr($value);
    }
}
