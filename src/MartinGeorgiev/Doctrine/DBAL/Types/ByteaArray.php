<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBytesArrayItemForPHPException;

/**
 * Implementation of PostgreSQL bytea[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-binary.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ByteaArray extends BaseStringArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BYTEA_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || \is_string($item);
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        \assert(\is_string($item));

        return '"\\\\x'.\bin2hex($item).'"';
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        $result = parent::transformArrayItemForPHP($item);

        if ($result === null) {
            return null;
        }

        if (\str_starts_with($result, '\\x')) {
            $decoded = \hex2bin(\substr($result, 2));

            return $decoded !== false ? $decoded : $result;
        }

        return $result;
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidBytesArrayItemForPHPException
    {
        return InvalidBytesArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidBytesArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidBytesArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
