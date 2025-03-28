<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

/**
 * Common validation logic for IP addresses and CIDR notation.
 *
 * @since 3.0
 */
trait NetworkAddressValidationTrait
{
    protected function isValidPlainIpAddress(string $value): bool
    {
        if ($this->isValidIpv4($value)) {
            return true;
        }

        return $this->isValidIpv6($value);
    }

    protected function isValidNetworkAddressWithCidr(string $value): bool
    {
        if (!$this->hasValidCidrFormat($value)) {
            return false;
        }

        [$ip, $netmask] = \explode('/', $value);
        $netmask = (int) $netmask;

        if ($this->isValidIpv4($ip)) {
            return $this->isValidIpv4Netmask($netmask);
        }

        if ($this->isValidIpv6($ip)) {
            return $this->isValidIpv6Netmask($netmask);
        }

        return false;
    }

    private function isValidIpv4(string $value): bool
    {
        return (bool) \filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    private function isValidIpv6(string $value): bool
    {
        return (bool) \filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    private function hasValidCidrFormat(string $value): bool
    {
        if (!\str_contains($value, '/')) {
            return false;
        }

        [$ip, $netmask] = \explode('/', $value);

        return \is_numeric($netmask);
    }

    private function isValidIpv4Netmask(int $netmask): bool
    {
        return $netmask >= 0 && $netmask <= 32;
    }

    private function isValidIpv6Netmask(int $netmask): bool
    {
        return $netmask >= 0 && $netmask <= 128;
    }
}
