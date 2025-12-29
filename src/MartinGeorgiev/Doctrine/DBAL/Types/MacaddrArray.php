<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrArrayItemForPHPException;

/**
 * Implementation of PostgreSQL MACADDR[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-net-types.html#DATATYPE-MACADDR
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class MacaddrArray extends BaseNetworkTypeArray
{
    protected const TYPE_NAME = Type::MACADDR_ARRAY;

    protected function isValidNetworkAddress(string $value): bool
    {
        // Check if it's using colons consistently
        if (\preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $value)) {
            return true;
        }

        // Check if it's using hyphens consistently
        // PostgreSQL requires MAC addresses to have separators
        return (bool) \preg_match('/^([0-9A-Fa-f]{2}-){5}[0-9A-Fa-f]{2}$/', $value);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidMacaddrArrayItemForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatException(mixed $value): never
    {
        throw InvalidMacaddrArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidMacaddrArrayItemForPHPException::forInvalidFormat($item);
    }
}
