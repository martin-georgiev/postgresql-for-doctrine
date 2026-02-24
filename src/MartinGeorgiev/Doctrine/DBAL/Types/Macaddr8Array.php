<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\Macaddr8ValidationTrait;

/**
 * Implementation of PostgreSQL MACADDR8[] data type (arrays of EUI-64 MAC addresses).
 *
 * @see https://www.postgresql.org/docs/current/datatype-net-types.html#DATATYPE-MACADDR8
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Macaddr8Array extends BaseNetworkTypeArray
{
    use Macaddr8ValidationTrait;

    protected const TYPE_NAME = Type::MACADDR8_ARRAY;

    protected function isValidNetworkAddress(string $value): bool
    {
        return $this->isValidMacaddr8Address($value);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidMacaddr8ArrayItemForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatException(mixed $value): never
    {
        throw InvalidMacaddr8ArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidMacaddr8ArrayItemForPHPException::forInvalidFormat($item);
    }
}
