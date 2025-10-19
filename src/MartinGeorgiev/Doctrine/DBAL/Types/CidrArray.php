<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCidrArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\CidrValidationTrait;

/**
 * Implementation of PostgreSQL CIDR[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-net-types.html#DATATYPE-CIDR
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class CidrArray extends BaseNetworkTypeArray
{
    use CidrValidationTrait;

    protected const TYPE_NAME = 'cidr[]';

    protected function isValidNetworkAddress(string $value): bool
    {
        return $this->isValidCidrAddress($value);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidCidrArrayItemForPHPException::forInvalidType($value);
    }

    protected function throwInvalidFormatException(mixed $value): never
    {
        throw InvalidCidrArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwInvalidItemException(): never
    {
        throw InvalidCidrArrayItemForPHPException::forInvalidFormat('');
    }
}
