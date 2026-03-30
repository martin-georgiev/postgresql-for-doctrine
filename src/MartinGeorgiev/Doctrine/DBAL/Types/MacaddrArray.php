<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddrArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\MacaddrValidationTrait;

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
    use MacaddrValidationTrait;

    protected const TYPE_NAME = Type::MACADDR_ARRAY;

    protected function isValidNetworkAddress(string $value): bool
    {
        return $this->isValidMacAddress($value);
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
