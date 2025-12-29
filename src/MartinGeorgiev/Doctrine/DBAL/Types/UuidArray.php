<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUuidArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidUuidArrayItemForPHPException;

/**
 * Implementation of PostgreSQL UUID[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-uuid.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class UuidArray extends BaseArray
{
    private const UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    protected const TYPE_NAME = Type::UUID_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!\is_string($item)) {
            throw InvalidUuidArrayItemForDatabaseException::forInvalidType($item);
        }

        return '"'.$item.'"';
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidUuid($item);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidUuidArrayItemForPHPException::forInvalidType($item);
        }

        $unquotedItem = \trim($item, '"');

        if (!$this->isValidUuid($unquotedItem)) {
            throw InvalidUuidArrayItemForPHPException::forInvalidFormat($item);
        }

        return $unquotedItem;
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidUuidArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(): never
    {
        throw InvalidUuidArrayItemForPHPException::forInvalidFormat('');
    }

    private function isValidUuid(string $value): bool
    {
        return (bool) \preg_match(self::UUID_REGEX, $value);
    }
}
