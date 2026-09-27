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
        return $this->isValidNetworkAddressWithCidr($value) && !$this->hasHostBitsSet($value);
    }

    /**
     * A cidr names a network, so PostgreSQL rejects one with a bit set right of its netmask, as in 192.168.1.1/24.
     * An inet holds a host address and keeps those bits, which is why the check lives here and not in the shared trait.
     */
    private function hasHostBitsSet(string $value): bool
    {
        [$address, $netmask] = \explode('/', $value);
        // inet_pton() refuses the leading zeros PostgreSQL reads in an IPv4 octet, such as 192.168.001.000
        $isIpv4 = !\str_contains($address, ':');
        $packedAddress = \inet_pton($isIpv4 ? (string) \preg_replace('/\b0+(\d+)\b/', '$1', $address) : $address);
        if ($packedAddress === false) {
            return false;
        }

        $addressBits = '';
        foreach (\str_split($packedAddress) as $byte) {
            $addressBits .= \sprintf('%08b', \ord($byte));
        }

        return \str_contains(\substr($addressBits, (int) $netmask), '1');
    }
}
