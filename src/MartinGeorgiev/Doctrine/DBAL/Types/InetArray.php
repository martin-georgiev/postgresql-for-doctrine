<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInetArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\InetValidationTrait;

/**
 * Implementation of PostgreSQL INET[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-net-types.html#DATATYPE-INET
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InetArray extends BaseNetworkTypeArray
{
    use InetValidationTrait;

    public const TYPE_NAME = 'inet[]';

    protected function isValidNetworkAddress(string $value): bool
    {
        return $this->isValidInetAddress($value);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidInetArrayItemForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatException(mixed $value): never
    {
        throw InvalidInetArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwInvalidItemException(): never
    {
        throw InvalidInetArrayItemForPHPException::forInvalidFormat('');
    }
}
